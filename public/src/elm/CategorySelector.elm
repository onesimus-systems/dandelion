module CategorySelector exposing
    ( Msg
    , State
    , categorySelector
    , categorySelectorStyled
    , init
    , toString
    , update
    )

import DandelionApi as Api
import Dialogs exposing (..)
import Html
import Html.Styled exposing (..)
import Html.Styled.Attributes exposing (..)
import Html.Styled.Events exposing (onInput)
import Http
import Styles as S



-- MODEL


type State
    = State StateValue


getStateValue : State -> StateValue
getStateValue state =
    case state of
        State stateValue ->
            stateValue


type alias StateValue =
    { current : List Api.Category
    , categories : List Api.Category
    }



-- STATE GETTERS


toString : State -> Maybe String
toString state =
    let
        stateValue =
            getStateValue state

        list =
            List.take (List.length stateValue.current - 1) stateValue.current

        str =
            List.map .desc list
                |> String.join ":"
    in
    if str == "" then
        Nothing

    else
        Just str


categoryChildren : List Api.Category -> Int -> List Api.Category
categoryChildren categories parent =
    List.filter (\c -> c.parent == parent) categories



-- INIT


init : ( State, Cmd Msg )
init =
    ( initialState, Api.categoriesGetAll HttpRespCategories )


initialState : State
initialState =
    State
        { current = [ { desc = "", id = 0, parent = 0 } ]
        , categories = []
        }



-- UPDATE


type alias ToMsg msg =
    State -> msg


type Msg
    = HttpRespCategories (Result Http.Error Api.CategoryGetAllResp)


update : Msg -> State -> State
update msg state =
    let
        stateValue =
            getStateValue state
    in
    case msg of
        HttpRespCategories result ->
            case result of
                Result.Ok resp ->
                    State { stateValue | categories = resp.data }

                Result.Err _ ->
                    state


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

        parent =
            parts
                |> List.drop 2
                |> List.head
                |> Maybe.andThen String.toInt
                |> Maybe.withDefault 0

        desc =
            parts
                |> List.drop 3
                |> List.head
                |> Maybe.withDefault ""

        newCategory =
            Api.Category desc id parent

        oldList =
            List.take level state.current

        newList =
            List.append
                (if id /= 0 then
                    List.append oldList [ newCategory ]

                 else
                    oldList
                )
                [ Api.Category "" 0 id ]
    in
    toMsg (State { state | current = newList })



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
        (List.indexedMap (viewCategorySelect state toMsg) state.current)


viewCategorySelect : StateValue -> ToMsg msg -> Int -> Api.Category -> Html msg
viewCategorySelect state toMsg level category =
    let
        levelStr =
            String.fromInt level

        children =
            categoryChildren state.categories category.parent
    in
    if List.length children == 0 then
        text ""

    else
        select
            [ css [ S.qbSelectStyle ]
            , onInput (updateCategorySelects state toMsg)
            ]
            (option [ value (levelStr ++ ":0:" ++ String.fromInt category.parent ++ ":") ] [ text "Select:" ]
                :: List.map
                    (viewCategoryOption levelStr category.id)
                    children
            )


viewCategoryOption : String -> Int -> Api.Category -> Html msg
viewCategoryOption level current category =
    option
        [ value
            (level
                ++ ":"
                ++ String.fromInt category.id
                ++ ":"
                ++ String.fromInt category.parent
                ++ ":"
                ++ category.desc
            )
        , selected (category.id == current)
        ]
        [ text category.desc ]
