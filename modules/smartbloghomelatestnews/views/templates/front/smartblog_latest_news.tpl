{*}
<div class="top_blog_block">
    <h2 class="title_block pull-left">{l s='From The Blog' mod='homeslideblogs'}</h2>
    <a href="javascript:void(0)" id="blog_next" class="btnbgcolorhoveronly next_slide pull-right">Next</a>
    <a href="javascript:void(0)" id="blog_prev" class="btnbgcolorhoveronly prev_slide pull-right">Prev</a>
</div>
<div id="blogs_wrap_ul" class="wrap_ul">
    <ul>
        {if isset($view_data) AND !empty($view_data)}
            {foreach from=$view_data item=post}
                    {assign var="options" value=null}
                    {$options.id_post = $post.id}
                    {$options.slug = $post.link_rewrite}
                    <li class="slide_item">
                        <a class="wrap_img" href="#"><img alt="{$post.title}" class="feat_img_small" src="{$modules_dir}smartblog/images/{$post.post_img}-home-default.jpg" /></a>
                        <h3 class="post_title_blog"><a href="#">{$post.title}</a></h3>
                        <p class="post_content_blog">
                            {$post.short_description|escape:'htmlall':'UTF-8'}
                        </p>
                        <p class="post_button_blog">
                            <a href="#"  class="btnbgcolorhover button">{l s='Read More' mod='smartbloghomelatestnews'}</a>
                        </p>
                    </li>
            {/foreach}
        {/if}
    </ul>
</div>
{*}
{assign var="current_option" value=Configuration::get('OVIC_CURRENT_OPTION')}
<div id="fashion_blog_hook" class="bleft{if isset($current_option)&& ($current_option ==1 || $current_option==3)} col-md-8{/if} pad-mbn-clr">
    <div class="top_blog_block">
        <h2 class="title_block pull-left">{l s='From The Blog' mod='smartbloghomelatestnews'}</h2>
        <a href="javascript:void(0)" id="blog_next" class="btnbgcolorhoveronly next_slide pull-right">Next</a>
        <a href="javascript:void(0)" id="blog_prev" class="btnbgcolorhoveronly prev_slide pull-right">Prev</a>
    </div>
    <div id="blogs_wrap_ul" class="wrap_ul">
        <ul>
            {if isset($view_data) AND !empty($view_data)}
                {foreach from=$view_data item=post}
                        {assign var="options" value=null}
                        {$options.id_post = $post.id}
                        {$options.slug = $post.link_rewrite}
                        <li class="slide_item">
                            <a class="wrap_img" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}"><img alt="{$post.title}" class="feat_img_small" src="{$modules_dir}smartblog/images/{$post.post_img}-home-default.jpg" /></a>
                            <h3 class="post_title_blog"><a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">{$post.title}</a></h3>
                            <p class="post_content_blog">
                                {$post.short_description|escape:'htmlall':'UTF-8'}
                            </p>
                            <p class="post_button_blog">
                                <a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}"  class="btnbgcolorhover button">{l s='Read More' mod='smartbloghomelatestnews'}</a>
                            </p>
                        </li>
                {/foreach}
            {/if}
        </ul>
    </div>
</div>
{assign var="current_option" value=Configuration::get('OVIC_CURRENT_OPTION')}
{if isset($current_option)&& ($current_option ==1 || $current_option==3)}
<script type="text/javascript">
    $(document).ready(function(){
        $(window).load(function(){
            var pelementWidth = $('#blogs_wrap_ul').width();
            var itemWidth = 370;  //width default
            var marginItem = 30;  //margin-right default
            var nItem = 2;  //number of items
            var perWidth = 48.3  //percent

            itemWidth = Math.floor(pelementWidth*perWidth/100);
            marginItem =  Math.floor((pelementWidth - (nItem*itemWidth))/(nItem-1));

            $('#blogs_wrap_ul li.slide_item').css('margin-right',marginItem+'px');
            $('#blogs_wrap_ul li.slide_item').css('width',itemWidth+'px');
        });

        $(window).resize(function(){
            var pelementWidth = $('#blogs_wrap_ul').width();
            var itemWidth = 370;  //width default
            var marginItem = 30;  //margin-right default
            var nItem = 2;  //number of items
            var perWidth = 48.3  //percent

            itemWidth = Math.floor(pelementWidth*perWidth/100);
            marginItem =  Math.floor((pelementWidth - (nItem*itemWidth))/(nItem-1));

            $('#blogs_wrap_ul li.slide_item').css('margin-right',marginItem+'px');
            $('#blogs_wrap_ul li.slide_item').css('width',itemWidth+'px');
        });
    })
</script>
{elseif isset($current_option)&& ($current_option ==2 || $current_option ==5)}
<script type="text/javascript">
    $(document).ready(function(){
        $(window).load(function(){
            var pelementWidth = $('#blogs_wrap_ul').width();
            var itemWidth = 370;  //width default
            var marginItem = 30;  //margin-right default
            var nItem = 3;  //number of items
            var perWidth = 31.7  //percent
            if (pelementWidth < 768){ldelim}
                nItem = 2;
                perWidth = 48.3;
            {rdelim}
            if (pelementWidth < 380){ldelim}
                nItem = 1;
                perWidth = 100;
            {rdelim}
            itemWidth = Math.floor(pelementWidth*perWidth/100);
            marginItem =  Math.floor((pelementWidth - (nItem*itemWidth))/(nItem-1));

            $('#blogs_wrap_ul li.slide_item').css('margin-right',marginItem+'px');
            $('#blogs_wrap_ul li.slide_item').css('width',itemWidth+'px');
        });

        $(window).resize(function(){
            var pelementWidth = $('#blogs_wrap_ul').width();
            var itemWidth = 370;  //width default
            var marginItem = 30;  //margin-right default
            var nItem = 3;  //number of items
            var perWidth = 31.7  //percent
            if (pelementWidth < 768){ldelim}
                nItem = 2;
                perWidth = 48.3;
            {rdelim}
            if (pelementWidth < 380){ldelim}
                nItem = 1;
                perWidth = 100;
            {rdelim}


            itemWidth = Math.floor(pelementWidth*perWidth/100);
            marginItem =  Math.floor((pelementWidth - (nItem*itemWidth))/(nItem-1));

            $('#blogs_wrap_ul li.slide_item').css('margin-right',marginItem+'px');
            $('#blogs_wrap_ul li.slide_item').css('width',itemWidth+'px');
        });
    })
</script>
{/if}