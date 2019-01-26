port module Main exposing (LogEntry, LogStatus(..), Model, Msg(..), PageOffsets, ViewSettings, bindDialogDragAndCenter, calcPageOffsets, detectOverflow, httpErrorToString, init, main, onEnter, reportOverflow, subscriptions, update, updateGotLogs, updateGotoPageOffset, updateQuickBuilderChanged, updateQuickBuilderChangedOk, updateRefreshLogsTick, updateReportOverflowIds, view, viewControlBar, viewFailureMsg, viewLoading, viewLogEntry, viewLogMetadata, viewLogOverflow, viewLogTable, viewPageControls, viewSearchControls)

import Browser
import Browser.Navigation as Navigation
import CheestoDialog as CD
import DandelionApi as Api
import Dialogs
import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (keyCode, on, onClick, onInput)
import Http
import Json.Decode as Decode exposing (decodeValue, int, list, string)
import Json.Encode as E
import Markdown
import QuickBuilder as QB
import Time



-- MAIN


main : Program ViewSettings Model Msg
main =
    Browser.element
        { init = init
        , view = view
        , update = update
        , subscriptions = subscriptions
        }



-- PORTS
-- Subscriptions


{-| Notify the application about log entries that have overflown their height
-}
port reportOverflow : (E.Value -> msg) -> Sub msg



-- Commands


{-| Tell JS to check for overflown log bodies, JS will issue a reportOverflow message
-}
port detectOverflow : E.Value -> Cmd msg


{-| Tell JS to bind mousedragging to an element with the given id
-}
port bindDialogDragAndCenter : E.Value -> Cmd msg



-- SUBSCRIPTIONS


subscriptions : Model -> Sub Msg
subscriptions model =
    Sub.batch
        [ reportOverflow ReportOverflowIds
        , Time.every 30000 RefreshLogsTick
        , if model.viewSettings.showCheesto then
            Time.every 30000 RefreshCheestoTick

          else
            Sub.none
        ]



-- MODEL


type alias Model =
    { logs : LogStatus
    , overflowLogIds : List Int
    , timedRefresh : Bool
    , viewSettings : ViewSettings
    , search : String
    , searching : Bool
    , page : PageOffsets
    , quickBuilderState : Maybe QB.State
    , cheesto : CheestoStatus
    , cheestoStatus : String
    , cheestoDialogState : Maybe CD.State
    }


type LogStatus
    = LogsLoading
    | LogsLoaded (List LogEntry)
    | LogsFailure String


type CheestoStatus
    = CheestoLoading
    | CheestoLoaded Api.CheestoApiData
    | CheestoFailure String


type alias LogEntry =
    Api.LogEntry


type alias ViewSettings =
    { showCreateBtn : Bool
    , showLog : Bool
    , showCheesto : Bool
    , cheestoEnabledClass : String
    }


type alias PageOffsets =
    { next : Int
    , prev : Int
    }



-- INIT


init : ViewSettings -> ( Model, Cmd Msg )
init settings =
    ( { logs = LogsLoading
      , overflowLogIds = []
      , timedRefresh = True
      , viewSettings = settings
      , search = ""
      , searching = False
      , page = PageOffsets -1 -1
      , quickBuilderState = Nothing
      , cheesto = CheestoLoading
      , cheestoStatus = ""
      , cheestoDialogState = Nothing
      }
    , Cmd.batch
        [ Api.logsGet GotLogs
        , if settings.showCheesto then
            Api.cheestoReadAll GotCheesto

          else
            Cmd.none
        ]
    )



-- UPDATE


type Msg
    = GotLogs (Result Http.Error Api.LogsApiResp)
    | SearchLogs String Int
    | RefreshLogsTick Time.Posix
    | RefreshCheestoTick Time.Posix
    | ReportOverflowIds E.Value
    | GotoPageOffset Int
    | ChangeSearchQuery String
    | StartSearch
    | ClearSearch
    | CreateNewLog
    | OpenSearchBuilder
    | CheckIfEnterSearch Int
    | QuickBuilderChanged QB.State (Cmd QB.Msg)
    | QuickBuilderMsg QB.Msg
    | GotCheesto (Result Http.Error Api.CheestoApiResp)
    | CheestoUpdateResp (Result Http.Error Api.ApiMetadata)
    | UpdateStatus String
    | CheestoDialogChanged CD.State


update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
    case msg of
        GotLogs result ->
            updateGotLogs result model

        UpdateStatus status ->
            if status == "Available" then
                ( model
                , Api.cheestoUpdate CheestoUpdateResp
                    { status = status
                    , returntime = "00:00:00"
                    , message = ""
                    }
                )

            else
                ( { model | cheestoStatus = status, cheestoDialogState = Just CD.init }
                , bindDialogDragAndCenter
                    (E.object
                        [ ( "target", E.string "dialog" )
                        , ( "trigger", E.string "dialog-header" )
                        ]
                    )
                )

        CheestoUpdateResp resp ->
            case resp of
                Ok metadata ->
                    ( model, Api.cheestoReadAll GotCheesto )

                Err _ ->
                    ( model, Cmd.none )

        SearchLogs query _ ->
            update StartSearch { model | search = query }

        RefreshLogsTick _ ->
            updateRefreshLogsTick model

        RefreshCheestoTick _ ->
            updateRefreshCheestoTick model

        ReportOverflowIds val ->
            updateReportOverflowIds val model

        GotoPageOffset val ->
            updateGotoPageOffset val model

        ChangeSearchQuery query ->
            ( { model | search = query }, Cmd.none )

        StartSearch ->
            ( { model | timedRefresh = False, searching = True }, Api.logsSearch GotLogs model.search 0 )

        ClearSearch ->
            ( { model | timedRefresh = True, search = "", searching = False }, Api.logsGet GotLogs )

        CreateNewLog ->
            ( model, Navigation.load "log/new" )

        OpenSearchBuilder ->
            let
                ( state, cmd ) =
                    QB.initialStateCmd
            in
            ( { model | quickBuilderState = Just state }
            , Cmd.batch
                [ Cmd.map QuickBuilderMsg cmd
                , bindDialogDragAndCenter
                    (E.object
                        [ ( "target", E.string "dialog" )
                        , ( "trigger", E.string "dialog-header" )
                        ]
                    )
                ]
            )

        CheckIfEnterSearch keycode ->
            if keycode == 13 then
                update StartSearch model

            else
                ( model, Cmd.none )

        QuickBuilderChanged state cmd ->
            updateQuickBuilderChanged state cmd model

        QuickBuilderMsg qbMsg ->
            case model.quickBuilderState of
                Just state ->
                    let
                        ( qbState, qbCmd ) =
                            QB.update qbMsg state
                    in
                    ( { model | quickBuilderState = Just qbState }, Cmd.map QuickBuilderMsg qbCmd )

                Nothing ->
                    ( model, Cmd.none )

        GotCheesto result ->
            updateGotCheesto result model

        CheestoDialogChanged state ->
            updateCheestoDialogChanged state model


updateGotLogs : Result Http.Error Api.LogsApiResp -> Model -> ( Model, Cmd Msg )
updateGotLogs result model =
    case result of
        Ok resp ->
            ( { model
                | logs = LogsLoaded resp.data.logs
                , page = calcPageOffsets resp.data.metadata
              }
            , detectOverflow E.null
            )

        Err err ->
            ( { model | logs = LogsFailure (httpErrorToString err) }, Cmd.none )


updateGotCheesto : Result Http.Error Api.CheestoApiResp -> Model -> ( Model, Cmd Msg )
updateGotCheesto result model =
    case result of
        Ok resp ->
            ( { model
                | cheesto = CheestoLoaded resp.data
              }
            , Cmd.none
            )

        Err err ->
            ( { model | cheesto = CheestoFailure (httpErrorToString err) }, Cmd.none )


updateRefreshLogsTick : Model -> ( Model, Cmd Msg )
updateRefreshLogsTick model =
    if model.logs /= LogsLoading && model.timedRefresh then
        ( model, Api.logsGet GotLogs )

    else
        ( model, Cmd.none )


updateRefreshCheestoTick : Model -> ( Model, Cmd Msg )
updateRefreshCheestoTick model =
    if model.cheesto /= CheestoLoading then
        ( model, Api.cheestoReadAll GotCheesto )

    else
        ( model, Cmd.none )


updateReportOverflowIds : E.Value -> Model -> ( Model, Cmd Msg )
updateReportOverflowIds val model =
    case decodeValue (list int) val of
        Ok ids ->
            ( { model | overflowLogIds = ids }, Cmd.none )

        Err _ ->
            ( model, Cmd.none )


updateGotoPageOffset : Int -> Model -> ( Model, Cmd Msg )
updateGotoPageOffset offset model =
    if model.searching then
        ( model, Api.logsSearch GotLogs model.search offset )

    else
        let
            timedRefresh =
                if offset == 0 then
                    True

                else
                    False
        in
        ( { model | timedRefresh = timedRefresh }, Api.logsGetWithOffset GotLogs offset )


updateQuickBuilderChanged : QB.State -> Cmd QB.Msg -> Model -> ( Model, Cmd Msg )
updateQuickBuilderChanged state cmd model =
    case QB.closedState state of
        QB.Not ->
            -- The builder is still open
            ( { model | quickBuilderState = Just state }, Cmd.map QuickBuilderMsg cmd )

        QB.Cancel ->
            -- The builder was cancelled
            ( { model | quickBuilderState = Nothing }, Cmd.none )

        QB.Ok ->
            -- The builder was okayed
            updateQuickBuilderChangedOk state model


updateQuickBuilderChangedOk : QB.State -> Model -> ( Model, Cmd Msg )
updateQuickBuilderChangedOk state model =
    let
        query =
            QB.toSearchQuery state
    in
    case query of
        Just q ->
            let
                ( newModel, cmd ) =
                    update (SearchLogs q 0) model
            in
            ( { newModel | quickBuilderState = Nothing }, cmd )

        Nothing ->
            ( { model | quickBuilderState = Nothing }, Cmd.none )


updateCheestoDialogChanged : CD.State -> Model -> ( Model, Cmd Msg )
updateCheestoDialogChanged state model =
    case CD.closedState state of
        CD.Not ->
            -- The dialog is still open
            ( { model | cheestoDialogState = Just state }, Cmd.none )

        CD.Cancel ->
            -- The builder was cancelled
            ( { model | cheestoDialogState = Nothing }, Cmd.none )

        CD.Ok ->
            -- The builder was okayed
            updateCheestoDialogChangedOk state model


updateCheestoDialogChangedOk : CD.State -> Model -> ( Model, Cmd Msg )
updateCheestoDialogChangedOk state model =
    let
        cheestoUpdate =
            CD.getUpdate state
    in
    ( { model | cheestoDialogState = Nothing }
    , Api.cheestoUpdate CheestoUpdateResp
        { status = model.cheestoStatus
        , returntime = cheestoUpdate.returntime
        , message = cheestoUpdate.message
        }
    )


httpErrorToString : Http.Error -> String
httpErrorToString err =
    case err of
        Http.BadUrl msg ->
            "Bad URL " ++ msg

        Http.Timeout ->
            "Network Timeout"

        Http.NetworkError ->
            "Network Error"

        Http.BadStatus status ->
            "Bad Status " ++ String.fromInt status

        Http.BadBody msg ->
            msg


calcPageOffsets : Api.LogsApiMetadata -> PageOffsets
calcPageOffsets metadata =
    let
        prevPage =
            if metadata.offset > 0 then
                metadata.offset - metadata.limit

            else
                -1

        nextPage =
            if metadata.offset + metadata.limit < metadata.logSize && metadata.resultCount == metadata.limit then
                metadata.offset + metadata.limit

            else
                -1
    in
    { next = nextPage
    , prev = prevPage
    }



-- VIEW


view : Model -> Html Msg
view model =
    div [ id "elm", class "main-info" ]
        [ button
            -- TODO: Connect to class toggling for sections
            [ type_ "button"
            , class "section-title disabled"
            , id "show-cheesto-button"
            ]
            [ text "Show Cheesto" ]
        , if model.viewSettings.showCheesto then
            viewStatusPanel model

          else
            text ""
        , button
            -- TODO: Connect to class toggling for sections
            [ type_ "button"
            , class "section-title disabled"
            , id "show-logs-button"
            ]
            [ text "Show Logs" ]
        , viewLogsPanel model
        ]


viewStatusPanel : Model -> Html Msg
viewStatusPanel model =
    div [ class "messages-panel" ]
        [ section [ id "messages-panel", class "messages-panel" ]
            [ span [ class "messages-title" ] [ text "Ĉeesto" ]
            , div [ id "messages-cheesto" ]
                (case model.cheesto of
                    CheestoLoading ->
                        [ text "Loading..." ]

                    CheestoFailure error ->
                        [ text error ]

                    CheestoLoaded cheesto ->
                        [ viewStatusSelect cheesto.statusOptions
                        , div [ class "__cheesto_status_table" ]
                            [ table [] (List.map viewCheestoStatus cheesto.statuses) ]
                        ]
                )
            ]
        , case model.cheestoDialogState of
            Just state ->
                div [] [ Dialogs.overlay, CD.cheestoDialog CheestoDialogChanged state ]

            Nothing ->
                text ""
        ]


viewStatusSelect : List String -> Html Msg
viewStatusSelect options =
    select
        [ class "__cheesto_status_select"
        , onInput UpdateStatus
        , value "-1"
        ]
        (option [ value "-1" ] [ text "Set Status:" ]
            :: List.map (\o -> option [ value o ] [ text o ]) options
        )


viewCheestoStatus : Api.CheestoStatus -> Html Msg
viewCheestoStatus status =
    let
        message =
            if status.message == "" then
                ""

            else
                status.message ++ "\n\n"
    in
    tr []
        [ td [] [ text status.fullname ]
        , if status.status == "Available" then
            td [ class "status-cell", title ("Last Changed: " ++ status.modified) ] [ text status.status ]

          else
            td
                [ class "status-cell"
                , title (message ++ "Return: " ++ status.returntime ++ "\nLast Changed: " ++ status.modified)
                ]
                [ text status.status ]
        ]


viewLogsPanel : Model -> Html Msg
viewLogsPanel model =
    div [ class "logs-panel", class ("" ++ model.viewSettings.cheestoEnabledClass) ]
        [ section [ id "logs-panel" ]
            [ viewControlBar model
            , if model.viewSettings.showLog then
                viewLogList model

              else
                text ""
            ]
        , case model.quickBuilderState of
            Just state ->
                div [] [ Dialogs.overlay, QB.quickBuilder QuickBuilderChanged state ]

            Nothing ->
                text ""
        ]


viewControlBar : Model -> Html Msg
viewControlBar model =
    div [ class "control-panel" ]
        [ viewSearchControls model
        , viewPageControls model
        ]


viewSearchControls : Model -> Html Msg
viewSearchControls model =
    div [ class "search-console" ]
        [ div [ class "search-buttons" ]
            [ button
                [ type_ "button"
                , id "query-builder-btn"
                , onClick OpenSearchBuilder
                ]
                [ text "Filter" ]
            ]
        , span [ class "query-box" ]
            [ input
                [ type_ "search"
                , id "search-query"
                , placeholder "Search"
                , value model.search
                , autocomplete False
                , onInput ChangeSearchQuery
                , onEnter StartSearch
                , autofocus True
                ]
                []
            ]
        , div [ class "search-buttons" ]
            [ button
                [ type_ "button"
                , id "search-btn"
                , onClick StartSearch
                ]
                [ text "Search" ]
            ]
        ]


viewPageControls : Model -> Html Msg
viewPageControls model =
    div [ class "top-controls" ]
        [ Html.form []
            [ if model.page.prev >= 0 then
                button
                    [ type_ "button"
                    , class "button"
                    , id "prev-page-button"
                    , onClick (GotoPageOffset model.page.prev)
                    ]
                    [ text "Prev" ]

              else
                text ""
            , if model.viewSettings.showCreateBtn then
                button
                    [ type_ "button"
                    , class "button"
                    , id "create-log-button"
                    , onClick CreateNewLog
                    ]
                    [ text "Create New" ]

              else
                text ""
            , if model.page.next >= 0 then
                button
                    [ type_ "button"
                    , class "button button-right"
                    , id "next-page-button"
                    , onClick (GotoPageOffset model.page.next)
                    ]
                    [ text "Next" ]

              else
                text ""
            , if model.searching then
                button
                    [ type_ "button"
                    , class "button button-right"
                    , id "clear-search-button"
                    , onClick ClearSearch
                    ]
                    [ text "Clear Search" ]

              else
                text ""
            ]
        ]


viewLogList : Model -> Html Msg
viewLogList model =
    case model.logs of
        LogsLoading ->
            viewLoading

        LogsLoaded logs ->
            viewLogTable model.overflowLogIds logs

        LogsFailure result ->
            viewFailureMsg result


viewLoading : Html Msg
viewLoading =
    div [ id "log-list" ]
        [ text "Loading Logs..."
        ]


viewLogTable : List Int -> List LogEntry -> Html Msg
viewLogTable overflowIds logs =
    div [ id "log-list" ]
        (List.map
            (\log -> viewLogEntry (List.member log.id overflowIds) log)
            logs
        )


viewLogEntry : Bool -> LogEntry -> Html Msg
viewLogEntry overflown log =
    div [ class "log-entry" ]
        [ span [ class "log-title" ]
            [ a [ href ("log/" ++ String.fromInt log.id) ]
                [ text log.title ]
            ]
        , div
            [ class "log-body"
            , attribute "data-log-id" (String.fromInt log.id)
            ]
          <|
            Markdown.toHtml
                -- Markdown parsing is a hacky way to display raw HTML
                Nothing
                log.body
        , viewLogOverflow log.id overflown
        , div [ class "log-metadata" ] (viewLogMetadata log)
        ]


viewLogOverflow : Int -> Bool -> Html Msg
viewLogOverflow id overflown =
    if overflown then
        div [ class "log-overflow" ]
            [ a
                [ href ("log/" ++ String.fromInt id)
                , target "_blank"
                ]
                [ text "Read more..."
                ]
            ]

    else
        div [] []


viewLogMetadata : LogEntry -> List (Html Msg)
viewLogMetadata log =
    [ span [ class "log-meta-author" ]
        [ text ("Created by " ++ log.fullname ++ " on " ++ log.dateCreated ++ " @ " ++ log.timeCreated)
        , if log.isEdited then
            text " (edited)"

          else
            text ""
        ]
    , span [ class "log-meta-cat" ]
        [ text "Categorized as "
        , a
            [ href "#"
            , class "category-search-link"
            , onClick (SearchLogs ("category:\"" ++ log.category ++ "\"") 0)
            ]
            [ text log.category ]
        ]
    , span [ class "log-meta-comments" ]
        [ text "Comments: "
        , a
            [ href ("log/" ++ String.fromInt log.id ++ "#comments")
            , class "category-search-link"
            ]
            [ text (String.fromInt log.numOfComments) ]
        ]
    ]


viewFailureMsg : String -> Html Msg
viewFailureMsg result =
    div [ id "log-list" ]
        [ text result ]


onEnter : Msg -> Attribute Msg
onEnter msg =
    let
        isEnter code =
            if code == 13 then
                Decode.succeed msg

            else
                Decode.fail "not ENTER"
    in
    on "keyup" (Decode.andThen isEnter keyCode)
