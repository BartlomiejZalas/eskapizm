{$linkfont|html_entity_decode}
<style type="text/css">
    body,.mainFont{ldelim}
        font-family:{$fontname};
    {rdelim}
    h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
        font-family: {$fontname};
    }
    /***** link color **********/
    .linkcolor{ldelim}
        color:{$linkcolor}!important;
    {rdelim}
    .linkcolor:hover{ldelim}
        color:{$linkHovercolor}!important;
    {rdelim}
    /****** button color ********/
    .btnbgcolor{ldelim}
        color:{$btncolor}!important;
    {rdelim}
    .btnbgcolor:hover{ldelim}
        color:{$btnHovercolor}!important;
    {rdelim}
    
    
    .mainColor,.mainHoverColor,.mainColorHoverOnly:hover, #homepage-slider .bx-wrapper .bx-controls-direction a:hover{ldelim}
        color:{$maincolor}!important;
    {rdelim}
    /**
     * color change on hover
     */
    .mainHoverColor:hover{ldelim}
        color:{$mainhovercolor}!important;
    {rdelim}
    /**
     * background not change on hover
     */
    .mainBgColor,.mainBgHoverColor,
    #homepage-slider .bx-wrapper .bx-pager.bx-default-pager a:hover, #homepage-slider .bx-wrapper .bx-pager.bx-default-pager a.active{ldelim}
        background-color:{$maincolor}!important;
    {rdelim}
    /**
     * background change on hover
     */
    .mainBgHoverColor:hover,.mainBgHoverOnly:hover{ldelim}
        background-color:{$mainhovercolor}!important;
    {rdelim}

    /**
     * border only hover
     */
    .mainBorderColor, .mainBorderHoverColor, #search_block_top, #nav_topmenu ul.nav > li:hover > a, #nav_topmenu ul.nav > li.active > a, .header_user_info a:hover{ldelim}
        border-color:{$mainhovercolor};
    {rdelim}
    .mainBorderLight, .mainBorderHoverColor:hover, .mainBorderHoverOnly:hover{ldelim}
        border-color:{$mainhovercolor}!important;
    {rdelim}
    #nav > li.open > a,#nav > li:hover > a.dropdown-toggle{ldelim}
        border-color:{$maincolor};
    {rdelim}
    dt.mainHoverColor:hover .product-name a{ldelim}
        color:{$maincolor};
    {rdelim}
    dt.mainHoverColor:hover .cart-images{ldelim}
        border-color:{$maincolor};
    {rdelim}

    #header, #search_block_top #search_query_top:focus{ldelim}
        background-color:{$grbacolor};
    {rdelim}
</style>