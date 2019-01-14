module QuickBuilder exposing (NotOkCancel(..), State, closedState, initialState, quickBuilder, toSearchQuery)

import Dialogs exposing (..)
import Html
import Html.Styled as HS exposing (..)
import Html.Styled.Attributes exposing (..)
import Html.Styled.Events exposing (keyCode, on, onCheck, onClick, onInput)
import Styles as S



-- MODEL


type alias State =
    InternalState


type InternalState
    = InternalState StateValue


getStateValue : InternalState -> StateValue
getStateValue state =
    case state of
        InternalState stateValue ->
            stateValue


type alias StateValue =
    { title : Field
    , body : Field
    , date1 : Field
    , date2 : Field
    , category : Field
    , closed : NotOkCancel
    }


type NotOkCancel
    = Not
    | Ok
    | Cancel


type alias Field =
    { value : String
    , not : Bool
    }


defaultField : Field
defaultField =
    { value = ""
    , not = False
    }


fieldValue : Field -> String
fieldValue field =
    field.value


changeFieldVal : String -> Field -> Field
changeFieldVal new field =
    { field | value = new }


changeFieldNot : Bool -> Field -> Field
changeFieldNot new field =
    { field | not = new }


isEmptyField : Field -> Bool
isEmptyField field =
    field.value == ""


type alias ToMsg msg =
    State -> msg



-- STATE GETTERS


closedState : State -> NotOkCancel
closedState state =
    let
        stateValue =
            getStateValue state
    in
    stateValue.closed


toSearchQuery : State -> Maybe String
toSearchQuery state =
    let
        stateValue =
            getStateValue state

        query =
            buildQueryPart stateValue.title "title"
                ++ buildQueryPart stateValue.body "body"
                ++ buildDateQuery stateValue.date1 stateValue.date2
                ++ buildQueryPart stateValue.category "category"
    in
    if query == "" then
        Nothing

    else
        Just query


buildQueryPart : Field -> String -> String
buildQueryPart field fieldName =
    if isEmptyField field then
        ""

    else
        let
            escapedFieldValue =
                String.replace "\"" "\\\"" field.value

            q =
                if field.not then
                    "!" ++ escapedFieldValue

                else
                    escapedFieldValue
        in
        " " ++ fieldName ++ ":\"" ++ q ++ "\""


buildDateQuery : Field -> Field -> String
buildDateQuery date1 date2 =
    if isEmptyField date1 then
        ""

    else
        let
            negate =
                if date1.not then
                    "!"

                else
                    ""
        in
        if isEmptyField date2 then
            " date:\"" ++ negate ++ date1.value ++ "\""

        else
            " date:\"" ++ negate ++ date1.value ++ " to " ++ date2.value ++ "\""



-- INIT


initialState : State
initialState =
    InternalState
        { title = defaultField
        , body = defaultField
        , date1 = defaultField
        , date2 = defaultField
        , category = defaultField
        , closed = Not
        }


quickBuilder : ToMsg msg -> State -> Html.Html msg
quickBuilder toMsg state =
    let
        stateValue =
            getStateValue state
    in
    toUnstyled (view stateValue toMsg)



-- UPDATE


type Msg
    = ChangeTitle
    | ChangeBody
    | ChangeDate1
    | ChangeDate2
    | ChangeCategory


updateTextInput : StateValue -> ToMsg msg -> Msg -> String -> msg
updateTextInput state toMsg msg val =
    case msg of
        ChangeTitle ->
            toMsg (InternalState { state | title = changeFieldVal val state.title })

        ChangeBody ->
            toMsg (InternalState { state | body = changeFieldVal val state.body })

        ChangeDate1 ->
            toMsg (InternalState { state | date1 = changeFieldVal val state.date1 })

        ChangeDate2 ->
            toMsg (InternalState { state | date2 = changeFieldVal val state.date2 })

        ChangeCategory ->
            toMsg (InternalState { state | category = changeFieldVal val state.category })


updateCheckInput : StateValue -> ToMsg msg -> Msg -> Bool -> msg
updateCheckInput state toMsg msg val =
    case msg of
        ChangeTitle ->
            toMsg (InternalState { state | title = changeFieldNot val state.title })

        ChangeBody ->
            toMsg (InternalState { state | body = changeFieldNot val state.body })

        ChangeDate1 ->
            toMsg (InternalState { state | date1 = changeFieldNot val state.date1 })

        ChangeDate2 ->
            toMsg (InternalState { state | date2 = changeFieldNot val state.date2 })

        ChangeCategory ->
            toMsg (InternalState { state | category = changeFieldNot val state.category })


updateClosedState : StateValue -> ToMsg msg -> Bool -> msg
updateClosedState state toMsg ok =
    let
        newClosedState =
            if ok then
                Ok

            else
                Cancel
    in
    toMsg (InternalState { state | closed = newClosedState })



-- VIEW


view : StateValue -> ToMsg msg -> Html msg
view state toMsg =
    dialog (viewDialogBuilder state toMsg) (updateClosedState state toMsg)


viewDialogBuilder : StateValue -> ToMsg msg -> List (Html msg)
viewDialogBuilder state toMsg =
    [ HS.form []
        [ viewDialogNotWithInput "qb-title" "Title:" (fieldValue state.title) state toMsg ChangeTitle
        , viewDialogNotWithInput "qb-body" "Body:" (fieldValue state.body) state toMsg ChangeBody
        , viewDialogDateRangeInput state toMsg

        -- , viewDialogCategoryMount
        ]
    ]


viewDialogNotWithInput : String -> String -> String -> StateValue -> ToMsg msg -> Msg -> Html msg
viewDialogNotWithInput baseName labelText val state toExternMsg toInternMsg =
    fieldset [ css [ S.qbFieldSetStyle ] ]
        [ label [ for baseName, css [ S.qbLabelStyle ] ] [ text labelText ]
        , text "Not: "
        , input
            [ type_ "checkbox"
            , css [ S.qbInputStyle ]
            , onCheck (updateCheckInput state toExternMsg toInternMsg)
            ]
            []
        , input
            [ type_ "text"
            , id baseName
            , value (Debug.log "value" val)
            , size 40
            , css [ S.qbInputStyle ]
            , onInput (updateTextInput state toExternMsg toInternMsg)
            ]
            []
        ]


viewDialogDateRangeInput : StateValue -> ToMsg msg -> Html msg
viewDialogDateRangeInput state toMsg =
    fieldset [ css [ S.qbFieldSetStyle ] ]
        [ label [ for "qb-date1", css [ S.qbLabelStyle ] ] [ text "Date:" ]
        , text "Not: "
        , input
            [ type_ "checkbox"
            , css [ S.qbInputStyle ]
            , onCheck (updateCheckInput state toMsg ChangeDate1)
            ]
            []
        , input
            [ type_ "text"
            , id "qb-date1"
            , class "qb-date"
            , value (fieldValue state.date1)
            , size 10
            , css [ S.qbInputStyle ]
            , onInput (updateTextInput state toMsg ChangeDate1)
            ]
            []
        , input
            [ type_ "text"
            , class "qb-date"
            , value (fieldValue state.date2)
            , size 10
            , css [ S.qbInputStyle ]
            , onInput (updateTextInput state toMsg ChangeDate2)
            ]
            []
        ]



-- viewDialogCategoryMount : Html msg
-- viewDialogCategoryMount =
--     fieldset [ css [ S.qbFieldSetStyle ] ]
--         [ label [ for "qb-cat", css [ S.qbLabelStyle ] ] [ text "Category:" ]
--         , text "Not: "
--         , input
--             [ type_ "checkbox"
--             , css [ S.qbInputStyle ]
--             ]
--             []
--         , span
--             [ id "qb-cat" ]
--             []
--         ]
