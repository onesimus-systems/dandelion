module QuickBuilder exposing
    ( Msg
    , NotOkCancel(..)
    , State
    , closedState
    , initialStateCmd
    , quickBuilder
    , toSearchQuery
    , update
    )

import CategorySelector as CS
import Dialogs exposing (..)
import Html
import Html.Styled as HS exposing (..)
import Html.Styled.Attributes exposing (..)
import Html.Styled.Events exposing (onCheck, onInput)
import Styles as S



-- STATE


type State
    = State StateValue


getStateValue : State -> StateValue
getStateValue state =
    case state of
        State stateValue ->
            stateValue


type alias StateValue =
    { title : Field
    , body : Field
    , date1 : Field
    , date2 : Field
    , categoryNot : Bool
    , csState : CS.State
    , closed : NotOkCancel
    }


type NotOkCancel
    = Not
    | Ok
    | Cancel



-- FIELD DATA


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



-- STATE GETTERS


closedState : State -> NotOkCancel
closedState state =
    (getStateValue state).closed


toSearchQuery : State -> Maybe String
toSearchQuery state =
    let
        stateValue =
            getStateValue state

        query =
            buildQueryPart stateValue.title "title"
                ++ buildQueryPart stateValue.body "body"
                ++ buildDateQuery stateValue.date1 stateValue.date2
                ++ buildCategoryQuery stateValue.csState stateValue.categoryNot
    in
    if query == "" then
        Nothing

    else
        Just (String.trim query)


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


buildCategoryQuery : CS.State -> Bool -> String
buildCategoryQuery csState invert =
    let
        catStr =
            CS.toString csState
                |> Maybe.withDefault ""
    in
    if catStr == "" then
        ""

    else
        let
            q =
                if invert then
                    "!" ++ catStr

                else
                    catStr
        in
        " category:\"" ++ q ++ "\""


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


initialStateCmd : ( State, Cmd Msg )
initialStateCmd =
    let
        ( csState, csCmd ) =
            CS.initialStateCmd
    in
    ( initialState csState
    , Cmd.map CategorySelectMsg csCmd
    )


initialState : CS.State -> State
initialState csState =
    State
        { title = defaultField
        , body = defaultField
        , date1 = defaultField
        , date2 = defaultField
        , categoryNot = False
        , csState = csState
        , closed = Not
        }



-- UPDATE


type alias ToMsg msg =
    State -> Cmd Msg -> msg


type IntMsg
    = ChangeTitle
    | ChangeBody
    | ChangeDate1
    | ChangeDate2
    | ChangeCategory CS.State (Cmd CS.Msg)
    | ChangeCategoryCheck


type Msg
    = CategorySelectMsg CS.Msg


update : Msg -> State -> ( State, Cmd Msg )
update msg state =
    let
        stateValue =
            getStateValue state
    in
    case msg of
        CategorySelectMsg csMsg ->
            let
                ( csState, csCmd ) =
                    CS.update csMsg stateValue.csState
            in
            ( State { stateValue | csState = csState }, Cmd.map CategorySelectMsg csCmd )


updateTextInput : StateValue -> ToMsg msg -> IntMsg -> String -> msg
updateTextInput state toMsg msg val =
    case msg of
        ChangeTitle ->
            toMsg (State { state | title = changeFieldVal val state.title }) Cmd.none

        ChangeBody ->
            toMsg (State { state | body = changeFieldVal val state.body }) Cmd.none

        ChangeDate1 ->
            toMsg (State { state | date1 = changeFieldVal val state.date1 }) Cmd.none

        ChangeDate2 ->
            toMsg (State { state | date2 = changeFieldVal val state.date2 }) Cmd.none

        ChangeCategory csState csCmd ->
            toMsg (State { state | csState = csState }) (Cmd.map CategorySelectMsg csCmd)

        _ ->
            toMsg (State state) Cmd.none


updateCheckInput : StateValue -> ToMsg msg -> IntMsg -> Bool -> msg
updateCheckInput state toMsg msg val =
    case msg of
        ChangeTitle ->
            toMsg (State { state | title = changeFieldNot val state.title }) Cmd.none

        ChangeBody ->
            toMsg (State { state | body = changeFieldNot val state.body }) Cmd.none

        ChangeDate1 ->
            toMsg (State { state | date1 = changeFieldNot val state.date1 }) Cmd.none

        ChangeDate2 ->
            toMsg (State { state | date2 = changeFieldNot val state.date2 }) Cmd.none

        ChangeCategoryCheck ->
            toMsg (State { state | categoryNot = val }) Cmd.none

        _ ->
            toMsg (State state) Cmd.none


updateClosedState : StateValue -> ToMsg msg -> Bool -> msg
updateClosedState state toMsg ok =
    let
        newClosedState =
            if ok then
                Ok

            else
                Cancel
    in
    toMsg (State { state | closed = newClosedState }) Cmd.none


updateCategory : StateValue -> ToMsg msg -> CS.State -> Cmd CS.Msg -> msg
updateCategory state toMsg csState csCmd =
    toMsg (State { state | csState = csState }) (Cmd.map CategorySelectMsg csCmd)



-- VIEW


quickBuilder : ToMsg msg -> State -> Html.Html msg
quickBuilder toMsg state =
    let
        stateValue =
            getStateValue state
    in
    toUnstyled (view stateValue toMsg)


view : StateValue -> ToMsg msg -> Html msg
view state toMsg =
    let
        defaultConfig =
            Dialogs.defaultDialogConfig
    in
    dialogWithConfig
        { defaultConfig | title = "Search Query Builder" }
        (viewDialogBuilder state toMsg)
        (updateClosedState state toMsg)


viewDialogBuilder : StateValue -> ToMsg msg -> List (Html msg)
viewDialogBuilder state toMsg =
    [ HS.form []
        [ viewDialogNotWithInput "qb-title" "Title:" state.title state toMsg ChangeTitle
        , viewDialogNotWithInput "qb-body" "Body:" state.body state toMsg ChangeBody
        , viewDialogDateRangeInput state toMsg
        , viewDialogCategoryMount state toMsg
        ]
    ]


viewDialogNotWithInput : String -> String -> Field -> StateValue -> ToMsg msg -> IntMsg -> Html msg
viewDialogNotWithInput baseName labelText field state toExternMsg toInternMsg =
    fieldset [ css [ S.qbFieldSetStyle ] ]
        [ label [ for baseName, css [ S.qbLabelStyle ] ] [ text labelText ]
        , text "Not: "
        , input
            [ type_ "checkbox"
            , css [ S.qbInputStyle ]
            , onCheck (updateCheckInput state toExternMsg toInternMsg)
            , checked field.not
            ]
            []
        , input
            [ type_ "text"
            , id baseName
            , value field.value
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
            , checked state.date1.not
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


viewDialogCategoryMount : StateValue -> ToMsg msg -> Html msg
viewDialogCategoryMount state toMsg =
    fieldset [ css [ S.qbFieldSetStyle ] ]
        [ label [ for "qb-cat", css [ S.qbLabelStyle ] ] [ text "Category:" ]
        , text "Not: "
        , input
            [ type_ "checkbox"
            , css [ S.qbInputStyle ]
            , onCheck (updateCheckInput state toMsg ChangeCategoryCheck)
            , checked state.categoryNot
            ]
            []
        , CS.categorySelectorStyled (updateCategory state toMsg) state.csState
        ]
