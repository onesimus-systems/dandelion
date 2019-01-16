module Styles exposing (black, bodyBgColor, buttonBgColor, buttonHoverBgColor, buttonHoverStyle, buttonHoverTextColor, buttonStyle, buttonTextColor, dandelionAccent, dandelionAccentLight, dandelionDarkGrey, dandelionDarkOrange, dandelionDarkRed, dandelionLightGrey, dandelionLightOrange, dandelionLightRed, dandelionMidGrey, dandelionMidOrange, dandelionMidRed, dialogBodyStyle, dialogFooterBtnStyle, dialogFooterStyle, dialogHeaderStyle, dialogStyle, navbarBgColor, navbarHoverBgColor, navbarHoverTextColor, navbarTextColor, noBorder, qbFieldSetStyle, qbInputStyle, qbLabelStyle, qbSelectStyle, selectElemStyle, white)

import Css exposing (..)



-- COLORS


black : Css.Color
black =
    rgb 0 0 0


white : Css.Color
white =
    rgb 255 255 255


dandelionLightOrange : Css.Color
dandelionLightOrange =
    hex "#ffb732"


dandelionMidOrange : Css.Color
dandelionMidOrange =
    hex "#ffa500"


dandelionDarkOrange : Css.Color
dandelionDarkOrange =
    hex "#996300"


dandelionLightGrey : Css.Color
dandelionLightGrey =
    hex "#c3c3c3"


dandelionMidGrey : Css.Color
dandelionMidGrey =
    hex "#636363"


dandelionDarkGrey : Css.Color
dandelionDarkGrey =
    hex "#363636"


dandelionLightRed : Css.Color
dandelionLightRed =
    hex "#ff3232"


dandelionMidRed : Css.Color
dandelionMidRed =
    hex "#b20000"


dandelionDarkRed : Css.Color
dandelionDarkRed =
    hex "#330000"


dandelionAccent : Css.Color
dandelionAccent =
    dandelionDarkOrange


dandelionAccentLight : Css.Color
dandelionAccentLight =
    dandelionMidOrange


bodyBgColor : Css.Color
bodyBgColor =
    hex "#dddddd"


navbarBgColor : Css.Color
navbarBgColor =
    dandelionDarkGrey


navbarHoverBgColor : Css.Color
navbarHoverBgColor =
    dandelionMidGrey


navbarTextColor : Css.Color
navbarTextColor =
    white


navbarHoverTextColor : Css.Color
navbarHoverTextColor =
    white


buttonTextColor : Css.Color
buttonTextColor =
    white


buttonBgColor : Css.Color
buttonBgColor =
    dandelionMidRed


buttonHoverTextColor : Css.Color
buttonHoverTextColor =
    white


buttonHoverBgColor : Css.Color
buttonHoverBgColor =
    hex "#cc1a1a"



-- GENERAL STYLES --


noBorder : Style
noBorder =
    border (px 0)


buttonStyle : Style
buttonStyle =
    Css.batch
        [ color buttonTextColor
        , backgroundColor buttonBgColor
        , padding2 (px 5) (px 10)
        , noBorder
        , hover [ buttonHoverStyle ]
        ]


buttonHoverStyle : Style
buttonHoverStyle =
    Css.batch
        [ color buttonHoverTextColor
        , backgroundColor buttonHoverBgColor
        , padding2 (px 5) (px 10)
        , noBorder
        , boxShadow none
        ]



-- QUICK BUILDER STYLES --


qbFieldSetStyle : Style
qbFieldSetStyle =
    border (px 0)


qbLabelStyle : Style
qbLabelStyle =
    Css.batch
        [ fontWeight bold
        , display block
        ]


qbInputStyle : Style
qbInputStyle =
    Css.batch
        [ margin2 (px 0) (px 5)
        , color black
        ]


selectElemStyle : Style
selectElemStyle =
    Css.batch
        [ border (px 0)
        , padding2 (px 0) (em 0.5)
        , fontSize (em 1)
        , height (em 1.5)
        , backgroundColor buttonBgColor
        , color buttonTextColor
        ]


qbSelectStyle : Style
qbSelectStyle =
    Css.batch
        [ selectElemStyle
        , marginTop (px 10)
        , marginRight (px 5)
        ]



-- DIALOG STYLES --


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
        [ backgroundColor dandelionMidGrey
        , fontSize (px 19)
        , padding (px 10)
        , color white
        , cursor move
        , fontWeight bold
        ]


dialogBodyStyle : Style
dialogBodyStyle =
    Css.batch
        [ backgroundColor dandelionDarkGrey
        , fontSize (Css.em 1.25)
        , padding (px 20)
        , color white
        ]


dialogFooterStyle : Style
dialogFooterStyle =
    Css.batch
        [ backgroundColor dandelionMidGrey
        , padding (px 10)
        , textAlign right
        ]


dialogFooterBtnStyle : Style
dialogFooterBtnStyle =
    Css.batch
        [ buttonStyle
        , marginLeft (px 5)
        , fontSize (em 1.25)
        ]
