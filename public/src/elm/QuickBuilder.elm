module QuickBuilder exposing
    ( Msg
    , NotOkCancel(..)
    , State
    , closedState
    , initialState
    , initialStateCmd
    , quickBuilder
    , toSearchQuery
    , update
    )

import Dialogs exposing (..)
import Html
import Html.Styled as HS exposing (..)
import Html.Styled.Attributes exposing (..)
import Html.Styled.Events exposing (keyCode, on, onCheck, onClick, onInput)
import Http
import Json.Decode as D
import Json.Decode.Pipeline exposing (optional, required)
import Json.Encode as E
import Styles as S
import Url.Builder as UB



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
    , category : CategoryField
    , categories : CategoryList
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


type alias CategoryField =
    { value : List Category
    , not : Bool
    }


defaultField : Field
defaultField =
    { value = ""
    , not = False
    }


defaultCategoryField : CategoryField
defaultCategoryField =
    { value = []
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


changeCategoryFieldVal : List Category -> CategoryField -> CategoryField
changeCategoryFieldVal new field =
    { field | value = new }


changeCategoryFieldNot : Bool -> CategoryField -> CategoryField
changeCategoryFieldNot new field =
    { field | not = new }


isEmptyCategoryField : CategoryField -> Bool
isEmptyCategoryField field =
    List.isEmpty field.value


type alias ToMsg msg =
    State -> Cmd Msg -> msg



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
                ++ buildCategoryQuery stateValue.category
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


buildCategoryQuery : CategoryField -> String
buildCategoryQuery field =
    if isEmptyCategoryField field then
        ""

    else
        let
            escapedFieldValue =
                String.join ":" (List.map (\c -> c.desc) field.value)

            q =
                if field.not then
                    "!" ++ escapedFieldValue

                else
                    escapedFieldValue
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
    ( initialState, getInitialCategoryList )


initialState : State
initialState =
    InternalState
        { title = defaultField
        , body = defaultField
        , date1 = defaultField
        , date2 = defaultField
        , category = defaultCategoryField
        , categories = CategoryList [] []
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


type IntMsg
    = ChangeTitle
    | ChangeBody
    | ChangeDate1
    | ChangeDate2
    | ChangeCategory


type Msg
    = HttpRespCategories (Result Http.Error CategoryList)


update : Msg -> State -> ( State, Cmd Msg )
update msg state =
    let
        stateValue =
            getStateValue state
    in
    case msg of
        HttpRespCategories result ->
            case result of
                Result.Ok cats ->
                    ( InternalState { stateValue | categories = Debug.log "cats: " cats }, Cmd.none )

                Result.Err _ ->
                    ( state, Cmd.none )


updateTextInput : StateValue -> ToMsg msg -> IntMsg -> String -> msg
updateTextInput state toMsg msg val =
    case msg of
        ChangeTitle ->
            toMsg (InternalState { state | title = changeFieldVal val state.title }) Cmd.none

        ChangeBody ->
            toMsg (InternalState { state | body = changeFieldVal val state.body }) Cmd.none

        ChangeDate1 ->
            toMsg (InternalState { state | date1 = changeFieldVal val state.date1 }) Cmd.none

        ChangeDate2 ->
            toMsg (InternalState { state | date2 = changeFieldVal val state.date2 }) Cmd.none

        ChangeCategory ->
            updateCategorySelects state toMsg val


updateCategorySelects : StateValue -> ToMsg msg -> String -> msg
updateCategorySelects state toMsg val =
    let
        parts =
            String.split ":" val

        level =
            parts
                |> List.head
                |> Maybe.andThen String.toInt
                |> Maybe.withDefault 0

        id =
            parts
                |> List.tail
                |> Maybe.andThen List.head
                |> Maybe.andThen String.toInt
                |> Maybe.withDefault 0

        desc =
            parts
                |> List.drop 2
                |> List.head
                |> Maybe.withDefault ""
    in
    -- TODO: Modify the categories state to track current selections and get next level
    toMsg (InternalState state) Cmd.none


updateCheckInput : StateValue -> ToMsg msg -> IntMsg -> Bool -> msg
updateCheckInput state toMsg msg val =
    case msg of
        ChangeTitle ->
            toMsg (InternalState { state | title = changeFieldNot val state.title }) Cmd.none

        ChangeBody ->
            toMsg (InternalState { state | body = changeFieldNot val state.body }) Cmd.none

        ChangeDate1 ->
            toMsg (InternalState { state | date1 = changeFieldNot val state.date1 }) Cmd.none

        ChangeDate2 ->
            toMsg (InternalState { state | date2 = changeFieldNot val state.date2 }) Cmd.none

        ChangeCategory ->
            toMsg (InternalState { state | category = changeCategoryFieldNot val state.category }) Cmd.none


updateClosedState : StateValue -> ToMsg msg -> Bool -> msg
updateClosedState state toMsg ok =
    let
        newClosedState =
            if ok then
                Ok

            else
                Cancel
    in
    toMsg (InternalState { state | closed = newClosedState }) Cmd.none



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
        , viewDialogCategoryMount state toMsg
        ]
    ]


viewDialogNotWithInput : String -> String -> String -> StateValue -> ToMsg msg -> IntMsg -> Html msg
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
            , value val
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


viewDialogCategoryMount : StateValue -> ToMsg msg -> Html msg
viewDialogCategoryMount state toMsg =
    fieldset [ css [ S.qbFieldSetStyle ] ]
        [ label [ for "qb-cat", css [ S.qbLabelStyle ] ] [ text "Category:" ]
        , text "Not: "
        , input
            [ type_ "checkbox"
            , css [ S.qbInputStyle ]
            , onCheck (updateCheckInput state toMsg ChangeCategory)
            ]
            []
        , div []
            (List.indexedMap (viewCategorySelect state toMsg) state.categories.levels)
        ]


viewCategorySelect : StateValue -> ToMsg msg -> Int -> List Category -> Html msg
viewCategorySelect state toMsg level categories =
    let
        levelStr =
            String.fromInt level
    in
    select
        [ css [ S.qbSelectStyle ]
        , onInput (updateTextInput state toMsg ChangeCategory)
        ]
        (option [] [ text "Select:" ]
            :: List.map
                (viewCategoryOption levelStr)
                categories
        )


viewCategoryOption : String -> Category -> Html msg
viewCategoryOption level category =
    option [ value (level ++ ":" ++ String.fromInt category.id ++ ":" ++ category.desc) ]
        [ text category.desc ]



-- HTTP


getInitialCategoryList : Cmd Msg
getInitialCategoryList =
    getCategoryList [ 0 ]


getCategoryList : List Int -> Cmd Msg
getCategoryList categories =
    let
        jsonList =
            E.encode 0 (E.list E.int categories)

        url =
            UB.absolute
                [ "render/categoriesJson" ]
                [ UB.string "pastSelection" jsonList ]
    in
    Http.get
        { url = url
        , expect = Http.expectJson HttpRespCategories respCategoriesDecoder
        }



-- JSON


type alias CategoryList =
    { currentList : List Int
    , levels : List (List Category)
    }


type alias Category =
    { desc : String
    , id : Int
    , selected : Bool
    }


respCategoriesDecoder : D.Decoder CategoryList
respCategoriesDecoder =
    D.succeed CategoryList
        |> required "currentList" (D.list D.int)
        |> required "levels" (D.list (D.list respCategoryDecoder))


respCategoryDecoder : D.Decoder Category
respCategoryDecoder =
    D.succeed Category
        |> required "desc" D.string
        |> required "id" D.int
        |> required "selected" D.bool
