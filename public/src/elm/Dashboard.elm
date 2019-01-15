port module Main exposing (ApiResponse, LogEntry, LogStatus(..), LogsApiData, LogsApiMetadata, LogsApiResp, Model, Msg(..), PageOffsets, ViewSettings, apiDecoder, calcPageOffsets, detectOverflow, getLogEntries, getLogEntriesUrl, getLogEntriesWithOffset, httpErrorToString, init, logDecoder, logsApiDataDecoder, logsApiRespDecoder, logsListDecoder, logsMetadataDecoder, logsMetadataEncoder, main, reportOverflow, searchApiUrl, searchLogsHttp, subscriptions, update, updateGotLogs, updateGotoPageOffset, updateNewLogs, updateRefreshLogsTick, updateReportOverflowIds, view, viewControlBar, viewFailureMsg, viewLoading, viewLogEntry, viewLogMetadata, viewLogOverflow, viewLogTable, viewPageControls, viewSearchControls)

import Browser
import Browser.Navigation as Navigation
import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (keyCode, on, onClick, onInput)
import Http
import Json.Decode as Decode exposing (Decoder, bool, decodeValue, int, list, string)
import Json.Decode.Pipeline exposing (optional, required)
import Json.Encode as E
import Markdown
import QuickBuilder as QB
import Time
import Url.Builder as UB



-- PORTS
-- Subscriptions


{-| Notify the application about log entries that have overflown their height
-}
port reportOverflow : (E.Value -> msg) -> Sub msg


port searchQueryExt : (E.Value -> msg) -> Sub msg



-- Commands


{-| Tell JS to check for overflown log bodies, JS will issue a reportOverflow message
-}
port detectOverflow : E.Value -> Cmd msg



-- MAIN


main : Program ViewSettings Model Msg
main =
    Browser.element
        { init = init
        , view = view
        , update = update
        , subscriptions = subscriptions
        }



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
    }


type LogStatus
    = Loading
    | Loaded (List LogEntry)
    | Failure String


type alias LogEntry =
    { body : String
    , canEdit : Bool
    , category : String
    , dateCreated : String
    , fullname : String
    , id : Int
    , isEdited : Bool
    , numOfComments : Int
    , timeCreated : String
    , title : String
    , userID : Int
    }


type alias ViewSettings =
    { showCreateBtn : Bool
    , showLog : Bool
    , cheestoEnabledClass : String
    }


type alias PageOffsets =
    { next : Int
    , prev : Int
    }


init : ViewSettings -> ( Model, Cmd Msg )
init settings =
    ( { logs = Loading
      , overflowLogIds = []
      , timedRefresh = True
      , viewSettings = settings
      , search = ""
      , searching = False
      , page = PageOffsets -1 -1
      , quickBuilderState = Nothing
      }
    , getLogEntries
    )



-- UPDATE


type Msg
    = GotLogs (Result Http.Error LogsApiResp)
    | SearchLogs String Int
    | SearchLogsExt E.Value
    | RefreshLogsTick Time.Posix
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


update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
    case msg of
        GotLogs result ->
            updateGotLogs result model

        SearchLogs query _ ->
            update StartSearch { model | search = query }

        SearchLogsExt val ->
            updateSearchLogsExt val model

        RefreshLogsTick _ ->
            updateRefreshLogsTick model

        ReportOverflowIds val ->
            updateReportOverflowIds val model

        GotoPageOffset val ->
            updateGotoPageOffset val model

        ChangeSearchQuery query ->
            ( { model | search = query }, Cmd.none )

        StartSearch ->
            ( { model | timedRefresh = False, logs = Loading, searching = True }, searchLogsHttp model.search 0 )

        ClearSearch ->
            ( { model | timedRefresh = True, logs = Loading, search = "", searching = False }, getLogEntries )

        CreateNewLog ->
            ( model, Navigation.load "log/new" )

        OpenSearchBuilder ->
            let
                ( state, cmd ) =
                    QB.initialStateCmd
            in
            ( { model | quickBuilderState = Just state }, Cmd.map QuickBuilderMsg cmd )

        CheckIfEnterSearch keycode ->
            if keycode == 13 then
                update StartSearch model

            else
                ( model, Cmd.none )

        QuickBuilderChanged state cmd ->
            updateQuickBuilderChanged (Debug.log "state" state) cmd model

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


updateGotLogs : Result Http.Error LogsApiResp -> Model -> ( Model, Cmd Msg )
updateGotLogs result model =
    case result of
        Ok resp ->
            ( { model
                | logs = Loaded resp.data.logs
                , page = calcPageOffsets resp.data.metadata
              }
            , detectOverflow E.null
            )

        Err err ->
            ( { model | logs = Failure (httpErrorToString err) }, Cmd.none )


updateSearchLogsExt : E.Value -> Model -> ( Model, Cmd Msg )
updateSearchLogsExt val model =
    case decodeValue string val of
        Ok query ->
            update StartSearch { model | search = query }

        Err err ->
            ( model, Cmd.none )


updateRefreshLogsTick : Model -> ( Model, Cmd Msg )
updateRefreshLogsTick model =
    if model.logs /= Loading && model.timedRefresh then
        ( model, getLogEntries )

    else
        ( model, Cmd.none )


updateNewLogs : E.Value -> Model -> ( Model, Cmd Msg )
updateNewLogs val model =
    case decodeValue logsApiRespDecoder val of
        Ok resp ->
            ( { model
                | logs = Loaded resp.data.logs
                , timedRefresh = False
                , page = calcPageOffsets resp.data.metadata
              }
            , detectOverflow E.null
            )

        Err err ->
            ( { model | logs = Failure (Decode.errorToString err) }, Cmd.none )


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
        ( model, searchLogsHttp model.search offset )

    else
        let
            timedRefresh =
                if offset == 0 then
                    True

                else
                    False
        in
        ( { model | logs = Loading, timedRefresh = timedRefresh }, getLogEntriesWithOffset offset )


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


calcPageOffsets : LogsApiMetadata -> PageOffsets
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



-- SUBSCRIPTIONS


subscriptions : Model -> Sub Msg
subscriptions _ =
    Sub.batch
        [ reportOverflow ReportOverflowIds
        , searchQueryExt SearchLogsExt
        , Time.every 30000 RefreshLogsTick
        ]



-- VIEW


view : Model -> Html Msg
view model =
    div []
        [ section [ id "logs-panel", class ("logs-panel " ++ model.viewSettings.cheestoEnabledClass) ]
            [ div []
                [ viewControlBar model
                , if model.viewSettings.showLog then
                    case model.logs of
                        Loading ->
                            viewLoading

                        Loaded logs ->
                            viewLogTable model.overflowLogIds logs

                        Failure result ->
                            viewFailureMsg result

                  else
                    div [] []
                ]
            ]
        , case model.quickBuilderState of
            Just state ->
                QB.quickBuilder QuickBuilderChanged state

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



-- HTTP


getLogEntriesWithOffset : Int -> Cmd Msg
getLogEntriesWithOffset offset =
    Http.get
        { url = getLogEntriesUrl offset
        , expect = Http.expectJson GotLogs logsApiRespDecoder
        }


getLogEntries : Cmd Msg
getLogEntries =
    getLogEntriesWithOffset 0


getLogEntriesUrl : Int -> String
getLogEntriesUrl offset =
    UB.absolute
        [ "api/i/logs/read" ]
        [ UB.int "offset" offset ]


searchLogsHttp : String -> Int -> Cmd Msg
searchLogsHttp query offset =
    Http.get
        { url = searchApiUrl query offset
        , expect = Http.expectJson GotLogs logsApiRespDecoder
        }


searchApiUrl : String -> Int -> String
searchApiUrl query offset =
    UB.absolute
        [ "api/i/logs/search" ]
        [ UB.string "query" query, UB.int "offset" offset ]



-- JSON


type alias ApiResponse a value =
    a -> Int -> String -> String -> String -> value


logsApiRespDecoder : Decoder LogsApiResp
logsApiRespDecoder =
    apiDecoder LogsApiResp logsApiDataDecoder


apiDecoder : ApiResponse a value -> Decoder a -> Decoder value
apiDecoder value decoder =
    Decode.succeed value
        |> required "data" decoder
        |> required "errorcode" int
        |> required "module" string
        |> required "requestTime" string
        |> required "status" string



-- JSON - Get logs api response


type alias LogsApiResp =
    { data : LogsApiData
    , errorcode : Int
    , moduleName : String
    , requestTime : String
    , status : String
    }


type alias LogsApiData =
    { logs : List LogEntry
    , metadata : LogsApiMetadata
    }


logsApiDataDecoder : Decoder LogsApiData
logsApiDataDecoder =
    Decode.succeed LogsApiData
        |> required "logs" logsListDecoder
        |> required "metadata" logsMetadataDecoder


type alias LogsApiMetadata =
    { offset : Int
    , limit : Int
    , logSize : Int
    , resultCount : Int
    }


logsMetadataDecoder : Decoder LogsApiMetadata
logsMetadataDecoder =
    Decode.succeed LogsApiMetadata
        |> required "offset" int
        |> required "limit" int
        |> required "logSize" int
        |> required "resultCount" int


logsMetadataEncoder : LogsApiMetadata -> E.Value
logsMetadataEncoder metadata =
    E.object
        [ ( "offset", E.int metadata.offset )
        , ( "limit", E.int metadata.limit )
        , ( "logSize", E.int metadata.logSize )
        , ( "resultCount", E.int metadata.resultCount )
        ]


logsListDecoder : Decoder (List LogEntry)
logsListDecoder =
    list logDecoder


logDecoder : Decoder LogEntry
logDecoder =
    Decode.succeed LogEntry
        |> required "body" string
        |> optional "canEdit" bool False
        |> required "category" string
        |> required "date_created" string
        |> required "fullname" string
        |> required "id" int
        |> required "is_edited" bool
        |> required "num_of_comments" int
        |> required "time_created" string
        |> required "title" string
        |> required "user_id" int
