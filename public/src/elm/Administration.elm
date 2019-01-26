port module Main exposing (Model, Msg(..), init, main, subscriptions, update, view)

import Browser
import CategorySelector as CS
import DandelionApi as Api
import Dialogs
import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (onClick, onInput)
import Http
import Json.Encode as E



-- PORTS
-- Commands


{-| Tell JS to bind mousedragging to an element with the given id
-}
port bindDialogDragAndCenter : E.Value -> Cmd msg



-- MAIN


main : Program () Model Msg
main =
    Browser.element
        { init = init
        , view = view
        , update = update
        , subscriptions = subscriptions
        }



-- SUBSCRIPTIONS


subscriptions : Model -> Sub Msg
subscriptions _ =
    Sub.none



-- MODEL


type alias Model =
    { csState : CS.State
    , showDialog : Maybe DialogType
    , dialogInput : String
    }


type DialogType
    = AddCategory
    | EditCategory
    | DeleteCategory



-- INIT


init : flags -> ( Model, Cmd Msg )
init _ =
    let
        ( csState, csCmd ) =
            CS.init
    in
    ( { csState = csState
      , showDialog = Nothing
      , dialogInput = ""
      }
    , Cmd.map CategorySelectMsg csCmd
    )



-- UPDATE


type Msg
    = CategorySelectMsg CS.Msg
    | CategoryStateChange CS.State
    | DialogInputChange String
    | OpenAddDialog
    | CloseAddDialog Bool
    | OpenEditDialog
    | CloseEditDialog Bool
    | OpenDeleteDialog
    | CloseDeleteDialog Bool
    | HttpResp (Result.Result Http.Error Api.ApiMetadata)


update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
    case msg of
        CategorySelectMsg csMsg ->
            let
                csState =
                    CS.update csMsg model.csState
            in
            ( { model | csState = csState }, Cmd.none )

        CategoryStateChange csState ->
            ( { model | csState = csState }, Cmd.none )

        DialogInputChange val ->
            ( { model | dialogInput = val }, Cmd.none )

        OpenAddDialog ->
            ( { model | showDialog = Just AddCategory }
            , bindDialogCommand
            )

        CloseAddDialog ok ->
            if ok then
                ( { model | showDialog = Nothing, dialogInput = "" }
                , CS.createNew HttpResp model.dialogInput model.csState
                )

            else
                ( { model | showDialog = Nothing, dialogInput = "" }, Cmd.none )

        OpenEditDialog ->
            let
                catString =
                    CS.toString model.csState
                        |> Maybe.withDefault ""

                strElements =
                    String.split ":" catString

                lastElement =
                    strElements
                        |> List.drop (List.length strElements - 1)
                        |> List.head
                        |> Maybe.withDefault ""
            in
            if catString == "" then
                ( model, Cmd.none )

            else
                ( { model | showDialog = Just EditCategory, dialogInput = lastElement }
                , bindDialogCommand
                )

        CloseEditDialog ok ->
            if ok then
                ( { model | showDialog = Nothing, dialogInput = "" }
                , CS.edit HttpResp model.dialogInput model.csState
                )

            else
                ( { model | showDialog = Nothing, dialogInput = "" }, Cmd.none )

        OpenDeleteDialog ->
            let
                catString =
                    CS.toString model.csState
                        |> Maybe.withDefault ""
            in
            if catString == "" then
                ( model, Cmd.none )

            else
                ( { model | showDialog = Just DeleteCategory }
                , bindDialogCommand
                )

        CloseDeleteDialog ok ->
            if ok then
                ( { model | showDialog = Nothing }, CS.delete HttpResp model.csState )

            else
                ( { model | showDialog = Nothing }, Cmd.none )

        HttpResp _ ->
            let
                ( csState, csCmd ) =
                    CS.init
            in
            ( { model | csState = csState }, Cmd.map CategorySelectMsg csCmd )


bindDialogCommand : Cmd msg
bindDialogCommand =
    bindDialogDragAndCenter
        (E.object
            [ ( "target", E.string "dialog" )
            , ( "trigger", E.string "dialog-header" )
            ]
        )



-- VIEW


view : Model -> Html Msg
view model =
    section []
        [ h2 [] [ text "Category Management" ]
        , div [ class "admin-div" ]
            [ button
                [ type_ "button"
                , id "add-category-button"
                , class "button"
                , style "margin-right" ".25em"
                , onClick OpenAddDialog
                ]
                [ text "Add Category" ]
            , button
                [ type_ "button"
                , id "edit-category-button"
                , class "button"
                , style "margin-right" ".25em"
                , onClick OpenEditDialog
                ]
                [ text "Edit Category" ]
            , button
                [ type_ "button"
                , id "delete-category-button"
                , class "button"
                , onClick OpenDeleteDialog
                ]
                [ text "Delete Category" ]
            , CS.categorySelector CategoryStateChange model.csState
            ]
        , case model.showDialog of
            Just msg ->
                case msg of
                    AddCategory ->
                        viewAddCategoryDialog model

                    EditCategory ->
                        viewEditCategoryDialog model

                    DeleteCategory ->
                        viewDeleteCategoryDialog model

            Nothing ->
                text ""
        ]


viewAddCategoryDialog : Model -> Html Msg
viewAddCategoryDialog model =
    let
        defaultConfig =
            Dialogs.defaultDialogConfig
    in
    Dialogs.dialogWithConfigUnstyled
        { defaultConfig | title = "Add Category", okBtnText = "Save" }
        (viewAddCategoryDialogBody model)
        CloseAddDialog


viewAddCategoryDialogBody : Model -> List (Html Msg)
viewAddCategoryDialogBody model =
    let
        catString =
            CS.toString model.csState
                |> Maybe.withDefault ""

        msg =
            if catString == "" then
                "Create new root category:"

            else
                "Add new category:"

        dispStr =
            if catString == "" then
                ""

            else
                catString ++ ":"
    in
    [ text msg
    , br [] []
    , text dispStr
    , br [] []
    , br [] []
    , input
        [ type_ "text"
        , id "new_category"
        , value model.dialogInput
        , onInput DialogInputChange
        ]
        []
    ]


viewEditCategoryDialog : Model -> Html Msg
viewEditCategoryDialog model =
    let
        catString =
            CS.toString model.csState
                |> Maybe.withDefault ""

        defaultConfig =
            Dialogs.defaultDialogConfig
    in
    if catString == "" then
        text ""

    else
        Dialogs.dialogWithConfigUnstyled
            { defaultConfig | title = "Edit Category", okBtnText = "Save" }
            (viewEditCategoryDialogBody model)
            CloseEditDialog


viewEditCategoryDialogBody : Model -> List (Html Msg)
viewEditCategoryDialogBody model =
    [ input
        [ type_ "text"
        , id "edited_category"
        , value model.dialogInput
        , onInput DialogInputChange
        ]
        []
    ]


viewDeleteCategoryDialog : Model -> Html Msg
viewDeleteCategoryDialog model =
    let
        catString =
            CS.toString model.csState
                |> Maybe.withDefault ""

        defaultConfig =
            Dialogs.defaultConfirmConfig
    in
    if catString == "" then
        text ""

    else
        Dialogs.dialogWithConfigUnstyled
            { defaultConfig | title = "Delete Category" }
            (viewDeleteCategoryDialogBody model)
            CloseDeleteDialog


viewDeleteCategoryDialogBody : Model -> List (Html Msg)
viewDeleteCategoryDialogBody model =
    let
        catString =
            CS.toString model.csState
                |> Maybe.withDefault ""
    in
    [ text "Are you sure you want to delete this category?"
    , br [] []
    , br [] []
    , text catString
    ]
