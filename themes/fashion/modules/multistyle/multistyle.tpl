{$linkfont|html_entity_decode}
<style type="text/css">
     /***** link color **********/
    .linkcolor, .linkcolorhover, .linkcolorhoveronly, ul.product_list.grid > li.hovered .product-container h5 a.product-name{ldelim}
        color:{$linkcolor}!important;
    {rdelim}
    .linkcolorhover:hover{ldelim}
        color:{$linkHovercolor}!important;
    {rdelim}
    /****** button color ********/
    .btnbgcolor, .btnbgcolorhover,#brands_slider .owl-prev:hover, #brands_slider .owl-next:hover, .btnbgcolorhoveronly:hover,
    #home-page-tabs > li.active a, #home-page-tabs > li a:hover{ldelim}
        background-color:{$btncolor}!important;
    {rdelim}
    .btnbgcolorhover:hover, .btnbglightcolorhoveronly:hover  {ldelim}
        background-color:{$btnHovercolor}!important;
    {rdelim}
    .button{ldelim}
        background-color:{$btncolor}!important;
        color:{$btntextcolor}!important;
    {rdelim}
    .button:hover{ldelim}
        background-color:{$btnHovercolor}!important;
        color:{$btntextHovercolor}!important;
    {rdelim}
    body,.mainFont{ldelim}
        font-family:{$fontname};
    {rdelim}
    h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
        font-family: {$fontname};
    }
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
    .mainBgColor,.mainBgHoverColor, .mainColorBgHoverOnly:hover,
    #homepage-slider .bx-wrapper .bx-pager.bx-default-pager a:hover, #homepage-slider .bx-wrapper .bx-pager.bx-default-pager a.active{ldelim}
        background-color:{$maincolor}!important;
    {rdelim}
    /**
     * background change on hover
     */
    .mainBgHoverColor:hover,.mainBgHoverOnly:hover, .mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar{ldelim}
        background-color:{$mainhovercolor}!important;
    {rdelim}
    .mainColorBg,.mCSB_scrollTools .mCSB_draggerRail{ldelim}
        background-color:{$maincolor}!important;
    {rdelim}
    /**
     * border only hover
     */
    .mainBorderColor,.mainBorderHoverColor,#search_block_top, #nav_topmenu ul.nav > li:hover > a, #nav_topmenu ul.nav > li.active > a,.option-1 .header_user_info a:hover{ldelim}
        border-color:{$maincolor}!important;
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
    dt.mainHoverColor:hover .cart-images, dt.mainHoverColor:hover .remove_link a{ldelim}
        border-color:{$maincolor}!important;
    {rdelim}
    #header{ldelim}
        background-color:{$grbacolor};
    {rdelim}
    /* Global style */
    .block .title_block, .block h4 {ldelim}
        background-color:{$blocktitlebg}!important;
        color:{$blocktitletext}!important;
    {rdelim}
    .button.button-large,
    .button.button-medium,
    .button.button-small {ldelim}
        background-color:{$btncolor}!important;
    {rdelim}
    .button.button-large:hover,
    .button.button-medium:hover,
    .button.button-small:hover,
    .button.button-large span:hover,
    .button.button-medium span:hover,
    .button.button-small span:hover {ldelim}
        background-color:{$btnHovercolor}!important;
    {rdelim}
    #my-account ul.myaccount-link-list li a i {ldelim}
        color:{$btncolor}!important;
    {rdelim}
    #categories_block_left .block_content,
    #categories_block_left li a {ldelim}
        background-color:{$categorycolor};
    {rdelim}
    #categories_block_left li span.grower:hover + a,
    #categories_block_left li a:hover,
    #categories_block_left li a.selected {ldelim}
        background-color:{$categoryhovercolor}!important;
    {rdelim}
    ul.product_list.list > li .right-block .right-block-content .functional-buttons,
    ul.product_list.grid > li .product-container .functional-buttons {ldelim}
        background-color:{$btncolor}!important;
    {rdelim}
    .blocktitlebg{ldelim}
        background-color:{$blocktitlebg}!important;
    {rdelim}
    .blocktitletext{ldelim}
        color:{$blocktitletext}!important;
    {rdelim}
    .categorybgcolor,.categorybghovercolor{ldelim}
        background-color:{$categorycolor}!important;
    {rdelim}
    .categorybghovercolor:hover{ldelim}
        background-color:{$categoryhovercolor}!important;
    {rdelim}
</style>
<script type="text/javascript">
    $(document).ready(function(){ldelim}
            $('#search_block_top #search_query_top').focus(function(){ldelim}
                if($(window).width() < 1200){ldelim}
                    $('#search_block_top').addClass('mainBgColor');
                {rdelim}
            {rdelim});
             $('#search_block_top #search_query_top').blur(function(){ldelim}
                if ($('#search_block_top').hasClass('mainBgColor')){ldelim}
                    $('#search_block_top').removeClass('mainBgColor');
                {rdelim}
            {rdelim});
    {rdelim});
</script>