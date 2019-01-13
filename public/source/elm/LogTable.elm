port module Main exposing
    ( ApiResponse
    , LogEntry
    , LogStatus(..)
    , LogsApiData
    , LogsApiMetadata
    , LogsApiResp
    , Model
    , Msg(..)
    , apiDecoder
    , getLogEntries
    , init
    , logDecoder
    , logsApiDataDecoder
    , logsListDecoder
    , logsMetadataDecoder
    , main
    , subscriptions
    , update
    , view
    , viewFailureMsg
    , viewLoading
    , viewLogEntry
    , viewLogMetadata
    , viewLogTable
    )

import Browser
import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (onClick)
import Http
import Json.Decode as Decode exposing (Decoder, bool, decodeString, decodeValue, field, int, list, map5, string)
import Json.Decode.Pipeline exposing (optional, required)
import Json.Encode as E
import Markdown
import Time



-- PORTS


{-| logList allows external JS to give us logs to display
-}
port logList : (E.Value -> msg) -> Sub msg


{-| Notify the application that it has control of the log list again
-}
port startTimedRefresh : (E.Value -> msg) -> Sub msg


port reportOverflow : (E.Value -> msg) -> Sub msg


{-| Send page information to JS so it can update button info
-}
port pageInfo : E.Value -> Cmd msg


{-| Send a search query to JS
-}
port searchQuery : E.Value -> Cmd msg


port detectOverflow : E.Value -> Cmd msg



-- MAIN


main : Program () Model Msg
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


init : () -> ( Model, Cmd Msg )
init _ =
    ( { logs = Loading
      , overflowLogIds = []
      , timedRefresh = True
      }
    , getLogEntries
    )



-- UPDATE


type Msg
    = GotLogs (Result Http.Error LogsApiResp)
    | NewLogs E.Value
    | SearchLogs String
    | StartTimedRefresh E.Value
    | RefreshLogsTick Time.Posix
    | ReportOverflowIds E.Value


update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
    case msg of
        GotLogs result ->
            updateGotLogs result model

        NewLogs val ->
            updateNewLogs val model

        SearchLogs query ->
            ( model, searchQuery (E.string query) )

        StartTimedRefresh _ ->
            ( { model | timedRefresh = True }, getLogEntries )

        RefreshLogsTick _ ->
            updateRefreshLogsTick model

        ReportOverflowIds val ->
            updateReportOverflowIds val model


updateGotLogs : Result Http.Error LogsApiResp -> Model -> ( Model, Cmd Msg )
updateGotLogs result model =
    case result of
        Ok resp ->
            ( { model | logs = Loaded resp.data.logs }
            , Cmd.batch
                [ pageInfo (logsMetadataEncoder resp.data.metadata)
                , detectOverflow E.null
                ]
            )

        Err err ->
            ( { model | logs = Failure (httpErrorToString err) }, Cmd.none )


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
            ( { model | logs = Loaded resp.data.logs, timedRefresh = False }
            , Cmd.batch
                [ pageInfo (logsMetadataEncoder resp.data.metadata)
                , detectOverflow E.null
                ]
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



-- SUBSCRIPTIONS


subscriptions : Model -> Sub Msg
subscriptions model =
    Sub.batch
        [ logList NewLogs
        , startTimedRefresh StartTimedRefresh
        , reportOverflow ReportOverflowIds
        , Time.every 30000 RefreshLogsTick
        ]



-- VIEW


view : Model -> Html Msg
view model =
    case model.logs of
        Loading ->
            viewLoading

        Loaded logs ->
            viewLogTable model.overflowLogIds logs

        Failure result ->
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
            , onClick (SearchLogs log.category)
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



-- HTTP


getLogEntries : Cmd Msg
getLogEntries =
    Http.get
        { url = "/api/i/logs/read"
        , expect = Http.expectJson GotLogs logsApiRespDecoder
        }



-- JSON


type alias ApiResponse a value =
    a -> Int -> String -> String -> String -> value


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
