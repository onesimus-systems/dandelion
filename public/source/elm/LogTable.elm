port module Main exposing (ApiResponse, LogEntry, LogStatus(..), LogsApiData, LogsApiMetadata, LogsApiResp, Model, Msg(..), apiDecoder, getLogEntries, init, logDecoder, logsApiDataDecoder, logsListDecoder, logsMetadataDecoder, main, subscriptions, update, view, viewFailureMsg, viewLoading, viewLogEntry, viewLogMetadata, viewLogTable)

import Browser
import Html exposing (..)
import Html.Attributes exposing (..)
import Http
import Json.Decode as Decode exposing (Decoder, bool, decodeString, field, int, list, map5, string)
import Json.Decode.Pipeline exposing (required)
import Json.Encode
import Markdown



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
    { logs : LogStatus }


type LogStatus
    = Loading
    | Loaded (List LogEntry)
    | Failure Http.Error


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
    ( { logs = Loading }, getLogEntries )



-- UPDATE


type Msg
    = GotLogs (Result Http.Error LogsApiResp)


update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
    case msg of
        GotLogs result ->
            case result of
                Ok resp ->
                    ( { logs = Loaded resp.data.logs }, Cmd.none )

                Err err ->
                    ( { logs = Failure err }, Cmd.none )



-- SUBSCRIPTIONS


subscriptions : Model -> Sub Msg
subscriptions model =
    -- TODO: Subscribe to time ticks to refresh log list
    Sub.none



-- VIEW


view : Model -> Html Msg
view model =
    case model.logs of
        Loading ->
            viewLoading

        Loaded logs ->
            viewLogTable logs

        Failure result ->
            viewFailureMsg result


viewLoading : Html Msg
viewLoading =
    div [ id "log-list" ]
        [ text "Loading Logs..."
        ]


viewLogTable : List LogEntry -> Html Msg
viewLogTable logs =
    div [ id "log-list" ] (List.map viewLogEntry logs)


viewLogEntry : LogEntry -> Html Msg
viewLogEntry log =
    div [ class "log-entry" ]
        [ span [ class "log-title" ]
            [ a [ href ("log/" ++ String.fromInt log.id) ]
                [ text log.title
                ]
            ]
        , div
            [ class "log-body"
            , attribute "data-log-id" (String.fromInt log.id)
            ]
          <|
            Markdown.toHtml
                Nothing
                log.body
        , div [ class "log-metadata" ] (viewLogMetadata log)
        ]


viewLogMetadata : LogEntry -> List (Html Msg)
viewLogMetadata log =
    -- TODO: Link category to search bar
    [ span [ class "log-meta-author" ]
        [ text ("Created by " ++ log.fullname ++ " on " ++ log.dateCreated ++ " @ " ++ log.timeCreated) ]
    , span [ class "log-meta-cat" ]
        [ text "Categorized as "
        , a [ href "#", class "category-search-link" ]
            [ text log.category ]
        ]
    , span [ class "log-meta-comments" ]
        [ text "Comments: "
        , a [ href ("log/" ++ String.fromInt log.id ++ "#comments"), class "category-search-link" ]
            [ text (String.fromInt log.numOfComments) ]
        ]
    ]


viewFailureMsg : Http.Error -> Html Msg
viewFailureMsg result =
    div [ id "log-list" ]
        [ case result of
            Http.BadUrl msg ->
                text ("Bad URL " ++ msg)

            Http.Timeout ->
                text "Network Timeout"

            Http.NetworkError ->
                text "Network Error"

            Http.BadStatus status ->
                text ("Bad Status " ++ String.fromInt status)

            Http.BadBody msg ->
                text msg
        ]



-- HTTP


getLogEntries : Cmd Msg
getLogEntries =
    Http.get
        { url = "/api/i/logs/read"
        , expect = Http.expectJson GotLogs (apiDecoder LogsApiResp logsApiDataDecoder)
        }



-- JSON


type alias ApiResponse a value =
    a -> Int -> String -> String -> String -> value


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


logsListDecoder : Decoder (List LogEntry)
logsListDecoder =
    list logDecoder


logDecoder : Decoder LogEntry
logDecoder =
    Decode.succeed LogEntry
        |> required "body" string
        |> required "canEdit" bool
        |> required "category" string
        |> required "date_created" string
        |> required "fullname" string
        |> required "id" int
        |> required "is_edited" bool
        |> required "num_of_comments" int
        |> required "time_created" string
        |> required "title" string
        |> required "user_id" int
