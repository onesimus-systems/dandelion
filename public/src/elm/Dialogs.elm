module Dialogs exposing (AlertConfig, DialogConfig, alert, alertWithConfig, confirm, dialog, dialogWithConfig, overlay)

import Css exposing (..)
import Html.Styled exposing (..)
import Html.Styled.Attributes exposing (class, css)
import Html.Styled.Events exposing (onClick)


dialogStyle : Style
dialogStyle =
    Css.batch
        [ position fixed
        , top (px 0)
        , left (px 0)
        , zIndex (int 11)
        ]


dialogHeaderStyle : Style
dialogHeaderStyle =
    Css.batch
        [ backgroundColor (hex "666")
        , fontSize (px 19)
        , padding (px 10)
        , color (hex "ddd")
        ]


dialogBodyStyle : Style
dialogBodyStyle =
    Css.batch
        [ backgroundColor (hex "333")
        , fontSize (Css.em 1.25)
        , padding (px 20)
        , color (hex "fff")
        ]


dialogFooterStyle : Style
dialogFooterStyle =
    Css.batch
        [ backgroundColor (hex "666")
        , padding (px 10)
        , textAlign right
        ]


dialogFooterBtnStyle : Style
dialogFooterBtnStyle =
    Css.batch
        [ margin2 (px 0) (Css.em 0.25)
        , fontSize (Css.em 1.25)
        ]


type alias DialogConfig =
    { title : String
    , okBtnText : String
    , cancelBtnText : String
    }


defaultConfirmConfig : DialogConfig
defaultConfirmConfig =
    { title = "Confirm"
    , okBtnText = "Yes"
    , cancelBtnText = "Cancel"
    }


defaultDialogConfig : DialogConfig
defaultDialogConfig =
    { title = "Dialog"
    , okBtnText = "Ok"
    , cancelBtnText = "Cancel"
    }


confirm : String -> (Bool -> msg) -> Html msg
confirm prompt toMsg =
    dialogWithConfig defaultConfirmConfig [ text prompt ] toMsg


dialog : List (Html msg) -> (Bool -> msg) -> Html msg
dialog prompt toMsg =
    dialogWithConfig defaultDialogConfig prompt toMsg


dialogWithConfig : DialogConfig -> List (Html msg) -> (Bool -> msg) -> Html msg
dialogWithConfig config prompt toMsg =
    div
        [ css [ dialogStyle ] ]
        [ div [ css [ dialogHeaderStyle ] ]
            [ span [ class "dialog-box-title" ] [ text config.title ] ]
        , div [ css [ dialogBodyStyle ] ]
            prompt
        , div [ css [ dialogFooterStyle ] ]
            [ button
                [ class "dialog-box-ok-btn"
                , css [ dialogFooterBtnStyle ]
                , onClick (toMsg True)
                ]
                [ text config.okBtnText ]
            , button
                [ class "dialog-box-cancel-btn"
                , css [ dialogFooterBtnStyle ]
                , onClick (toMsg False)
                ]
                [ text config.cancelBtnText ]
            ]
        ]


type alias AlertConfig =
    { title : String
    , okBtnText : String
    }


defaultAlertConfig : AlertConfig
defaultAlertConfig =
    { title = "Message"
    , okBtnText = "Ok"
    }


alert : List (Html msg) -> msg -> Html msg
alert prompt toMsg =
    alertWithConfig defaultAlertConfig prompt toMsg


alertWithConfig : AlertConfig -> List (Html msg) -> msg -> Html msg
alertWithConfig config prompt toMsg =
    div
        [ css [ dialogStyle ] ]
        [ div [ css [ dialogHeaderStyle ] ]
            [ span [ class "dialog-box-title" ] [ text config.title ] ]
        , div [ css [ dialogBodyStyle ] ]
            prompt
        , div [ css [ dialogFooterStyle ] ]
            [ button
                [ class "dialog-box-ok-btn"
                , css [ dialogFooterBtnStyle ]
                , onClick toMsg
                ]
                [ text config.okBtnText ]
            ]
        ]


overlay : Html msg
overlay =
    div
        [ css
            [ position absolute
            , top (px 0)
            , left (px 0)
            , zIndex (int 10)
            , opacity (num 0.2)
            , backgroundColor (rgb 0 0 0)
            , width (pct 100)
            , height (pct 100)
            ]
        ]
        []
