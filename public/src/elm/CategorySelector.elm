module CategorySelector exposing
    ( Msg
    , State
    , categorySelector
    , categorySelectorStyled
    , initialStateCmd
    , toString
    , update
    )

import Dialogs exposing (..)
import Html
import Html.Styled exposing (..)
import Html.Styled.Attributes exposing (..)
import Html.Styled.Events exposing (keyCode, on, onCheck, onClick, onInput)
import Http
import Json.Decode as D
import Json.Encode as E
import Styles as S
import Url.Builder as UB



-- MODEL


type State
    = State StateValue


getStateValue : State -> StateValue
getStateValue state =
    case state of
        State stateValue ->
            stateValue


type alias StateValue =
    { current : List Category
    , levelData : List (List Category)
    }



-- STATE GETTERS


toString : State -> Maybe String
toString state =
    let
        stateValue =
            getStateValue state

        str =
            List.map (\c -> c.desc) stateValue.current
                |> String.join ":"
    in
    if str == "" then
        Nothing

    else
        Just str


toIdList : List Category -> List Int
toIdList categories =
    List.map (\c -> c.id) categories



-- INIT


initialStateCmd : ( State, Cmd Msg )
initialStateCmd =
    ( initialState, getInitialCategoryList )


initialState : State
initialState =
    State
        { current = []
        , levelData = []
        }



-- UPDATE


type alias ToMsg msg =
    State -> Cmd Msg -> msg


type Msg
    = HttpRespCategories (Result Http.Error CategoryListApi)


update : Msg -> State -> ( State, Cmd Msg )
update msg state =
    let
        stateValue =
            getStateValue state
    in
    case msg of
        HttpRespCategories result ->
            case result of
                Result.Ok levels ->
                    ( State { stateValue | levelData = levels }, Cmd.none )

                Result.Err _ ->
                    ( state, Cmd.none )


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

        newCategory =
            Category desc id False

        oldList =
            List.take level state.current

        newList =
            if id /= 0 then
                List.append oldList [ newCategory ]

            else
                oldList
    in
    toMsg (State { state | current = newList }) (getCategoryList (0 :: toIdList newList))



-- VIEW


categorySelector : ToMsg msg -> State -> Html.Html msg
categorySelector toMsg state =
    toUnstyled (categorySelectorStyled toMsg state)


categorySelectorStyled : ToMsg msg -> State -> Html msg
categorySelectorStyled toMsg state =
    let
        stateValue =
            getStateValue state
    in
    view stateValue toMsg


view : StateValue -> ToMsg msg -> Html msg
view state toMsg =
    div []
        (List.indexedMap (viewCategorySelect state toMsg) state.levelData)


viewCategorySelect : StateValue -> ToMsg msg -> Int -> List Category -> Html msg
viewCategorySelect state toMsg level categories =
    let
        levelStr =
            String.fromInt level
    in
    select
        [ css [ S.qbSelectStyle ]
        , onInput (updateCategorySelects state toMsg)
        ]
        (option [ value (levelStr ++ ":0:") ] [ text "Select:" ]
            :: List.map
                (viewCategoryOption levelStr)
                categories
        )


viewCategoryOption : String -> Category -> Html msg
viewCategoryOption level category =
    option
        [ value (level ++ ":" ++ String.fromInt category.id ++ ":" ++ category.desc)
        , selected category.selected
        ]
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


type alias CategoryListApi =
    List (List Category)


type alias Category =
    { desc : String
    , id : Int
    , selected : Bool
    }


respCategoriesDecoder : D.Decoder CategoryListApi
respCategoriesDecoder =
    D.field "levels" (D.list (D.list respCategoryDecoder))


respCategoryDecoder : D.Decoder Category
respCategoryDecoder =
    D.map3 Category
        (D.field "desc" D.string)
        (D.field "id" D.int)
        (D.field "selected" D.bool)
