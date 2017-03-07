<div itemtype="#" itemscope="" class="sdsarticleCat clearfix">

    {$date=date_create($post.created)}
    <div class="datefr">
        <span class="month_post">{date_format($date,"M")}</span>
        <span class="day_post">{date_format($date,"d")}</span>
        <span class="year_post">{date_format($date,"Y")}</span>
    </div>

    <div id="smartblogpost-{$post.id_post}" class="smartblogpost-item">
    {assign var="options" value=null}
    {$options.id_post = $post.id_post} 
    {$options.slug = $post.link_rewrite}
    <div class="articleContent">
          <a itemprop="url" href='{smartblog::GetSmartBlogLink('smartblog_post',$options)}' title="{$post.meta_title}" class="imageFeaturedLink">
                    {assign var="activeimgincat" value='0'}
                    {$activeimgincat = $smartshownoimg} 
                    {if ($post.post_img != "no" && $activeimgincat == 0) || $activeimgincat == 1}
              <img itemprop="image" alt="{$post.meta_title}" src="{$modules_dir}/smartblog/images/{$post.post_img}-single-default.jpg" class="imageFeatured">
                    {/if}
          </a>
    </div>
    <div class="sdsarticleHeader">
                            <p class='sds_title_block'><a title="{$post.meta_title}" href='{smartblog::GetSmartBlogLink('smartblog_post',$options)}'>{$post.meta_title}</a></p>
             {assign var="options" value=null}
                        {$options.id_post = $post.id_post}
                        {$options.slug = $post.link_rewrite}
               {assign var="catlink" value=null}
                            {$catlink.id_category = $post.id_category}
                            {$catlink.slug = $post.cat_link_rewrite}
         <span>{l s='Posted by:' mod='smartblog'} <span itemprop="author">{if $smartshowauthor ==1}&nbsp; {if $smartshowauthorstyle != 0}{$post.firstname} {$post.lastname}{else}{$post.lastname} {$post.firstname}{/if}</span>{/if} &nbsp;&nbsp;/&nbsp; <span itemprop="articleSection"><a href="{smartblog::GetSmartBlogLink('smartblog_category',$catlink)}">{if $title_category != ''}{$title_category}{else}{$post.cat_name}{/if}</a></span> &nbsp;<span class="comment"> &nbsp;/&nbsp; <a title="{$post.totalcomment} Comments" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}#articleComments">{l s='Comments: ' mod='smartblog'}{$post.totalcomment}</a></span>{if $smartshowviewed ==1}&nbsp; /{l s=' Views: ' mod='smartblog'} {$post.viewed}{/if}</span>
    </div>
    
           <div class="sdsarticle-des">
          <span itemprop="description" class="clearfix"><div id="lipsum">
	{$post.short_description}</div></span>
         </div>
        <div class="sdsreadMore">
                  {assign var="options" value=null}
                        {$options.id_post = $post.id_post}  
                        {$options.slug = $post.link_rewrite}  
                         <span class="more"><a title="{$post.meta_title}" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}" class="r_more">{l s='Read more' mod='smartblog'} </a></span>
        </div>
   </div>
</div>