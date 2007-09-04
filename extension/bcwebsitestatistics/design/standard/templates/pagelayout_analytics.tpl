<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$site.http_equiv.Content-language|wash}" lang="{$site.http_equiv.Content-language|wash}">
<head>
{def $basket_is_empty   = cond( $current_user.is_logged_in, fetch( shop, basket ).is_empty, 1 )
     $current_node_id   = first_set( $module_result.node_id, 0 )
     $user_hash         = concat( $current_user.role_id_list|implode( ',' ), ',', $current_user.limited_assignment_value_list|implode( ',' ) )
     $user_id           = $current_user.contentobject_id}

{if and( $current_node_id|eq(0), is_set( $module_result.path.0 ) , is_set( $module_result.path[$module_result.path|count|dec].node_id ) )}
	{set $current_node_id = $module_result.path[$module_result.path|count|dec].node_id}
{/if}

{cache-block keys=array( $uri_string, $basket_is_empty, $user_id )}
{def $pagestyle        = 'nosidemenu noextrainfo'
     $locales          = fetch( 'content', 'translation_list' )
     $pagerootdepth    = ezini( 'SiteSettings', 'RootNodeDepth', 'site.ini' )
     $indexpage        = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $infobox_count    = 0
     $path_normalized  = ''
     $path_array       = array()
     $pagedesign_class = fetch( 'content', 'class', hash( 'class_id', 'template_look' ) )
     $pagedepth        = $module_result.path|count
     $content_info     = hash()
}

{if $pagedesign_class.object_count|eq( 0 )|not}
    {def $pagedesign = $pagedesign_class.object_list[0]}
{/if}

{if is_set( $module_result.content_info )}
    {set $content_info = $module_result.content_info}
{/if}

{include uri='design:page_head.tpl'}
<style type="text/css">
    @import url({"stylesheets/core.css"|ezdesign(no)});
    @import url({"stylesheets/pagelayout.css"|ezdesign(no)});
    @import url({"stylesheets/content.css"|ezdesign(no)});
    @import url({"stylesheets/websitetoolbar.css"|ezdesign(no)});
    @import url({ezini('StylesheetSettings','ClassesCSS','design.ini')|ezroot(no)});
    @import url({ezini('StylesheetSettings','SiteCSS','design.ini')|ezroot(no)});
    {foreach ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ) as $css_file}
    @import url({concat( 'stylesheets/', $css_file )|ezdesign});
    {/foreach}
</style>
<link rel="stylesheet" type="text/css" href={"stylesheets/print.css"|ezdesign} media="print" />
<!-- IE conditional comments; for bug fixes for different IE versions -->
<!--[if IE 5]>     <style type="text/css"> @import url({"stylesheets/browsers/ie5.css"|ezdesign(no)});    </style> <![endif]-->
<!--[if lte IE 7]> <style type="text/css"> @import url({"stylesheets/browsers/ie7lte.css"|ezdesign(no)}); </style> <![endif]-->
{foreach ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ) as $script}
    <script language="javascript" type="text/javascript" src={concat( 'javascript/', $script )|ezdesign}></script>
{/foreach}

{'false'|bc_ga_urchin()}
</head>
<body>
<!-- Complete page area: START -->

{if $pagerootdepth|not}
    {set $pagerootdepth = 1}
{/if}

{if and( is_set( $content_info.class_identifier ), ezini( 'MenuSettings', 'HideLeftMenuClasses', 'menu.ini' )|contains( $content_info.class_identifier ) )}
    {set $pagestyle = 'nosidemenu noextrainfo'}
{elseif and( eq( $ui_context, 'edit' ), $uri_string|contains("content/versionview")|not )}
    {set $pagestyle       = 'nosidemenu noextrainfo'}
{elseif eq( $ui_context, 'browse' )}
    {set $pagestyle       = 'nosidemenu noextrainfo'}
{elseif $current_node_id}
	{if is_set( $module_result.path[$pagerootdepth|dec].node_id )}
		{set $indexpage = $module_result.path[$pagerootdepth|dec].node_id}
	{/if}
	{if is_set( $module_result.path[1] )}
	    {set $infobox_count = fetch( 'content', 'list_count', hash( 'parent_node_id', $current_node_id,
                                                                    'class_filter_type', 'include',
                                                                    'class_filter_array', array( 'infobox' ) ) )}
	    {if ne( $infobox_count , 0 ) }
	        {set $pagestyle = 'sidemenu extrainfo'}
	    {else}
	        {set $pagestyle = 'sidemenu noextrainfo'}
	    {/if}
	{/if}
{/if}

{if is_set($module_result.section_id)}
    {set $pagestyle = concat( $pagestyle, " section_id_", $module_result.section_id )}
{/if}

{foreach $module_result.path as $index => $path}
    {if $index|ge($pagerootdepth)}
        {set $path_array = $path_array|append($path)}
    {/if}
    {if is_set($path.node_id)}
        {set $path_normalized = $path_normalized|append( concat('subtree_level_', $index, '_node_id_', $path.node_id, ' ' ))}
    {/if}
{/foreach}

<!-- Change between "sidemenu"/"nosidemenu" and "extrainfo"/"noextrainfo" to switch display of side columns on or off  -->
<div id="page" class="{$pagestyle} {$path_normalized|trim()} current_node_id_{$current_node_id}">

  <!-- Header area: START -->
  <div id="header" class="float-break">
  <div id="usermenu">
    <div id="languages">
        {if $locales|count|gt( 1 )}
        <ul>
        {foreach $pagedesign.data_map.language_settings.content.rows.sequential as $row}
        {def $site_url = $row.columns[0]
             $language = $row.columns[2]}
        {if $row.columns[0]}
            {set $site_url = $site_url|append( "/" )}
            <li{if $row.columns[1]|downcase()|eq($access_type.name)} class="current_siteaccess"{/if}>
	        {if is_set($DesignKeys:used.url_alias)}
	            <a href="{concat( "http://", $site_url,
	                     $DesignKeys:used.url_alias
	                     )}">{$language}</a>
	        {else}
	            <a href="{concat( "http://", $site_url,
	                     $uri_string
                         )}">{$language}</a>
            {/if}
            </li>
        {/if}
        {undef $site_url $language}
        {/foreach}
        </ul>
        {/if}
    </div>
    <div id="links">
        <ul>
            {if $pagedesign.data_map.tag_cloud_url.data_text|ne('')}
                {if $pagedesign.data_map.tag_cloud_url.content|eq('')}
                <li><a href={concat("/content/view/tagcloud/", $indexpage)|ezurl} title="{$pagedesign.data_map.tag_cloud_url.data_text|wash}">{$pagedesign.data_map.tag_cloud_url.data_text|wash}</a></li>
                {else}
                <li><a href={$pagedesign.data_map.tag_cloud_url.content|ezurl} title="{$pagedesign.data_map.tag_cloud_url.data_text|wash}">{$pagedesign.data_map.tag_cloud_url.data_text|wash}</a></li>
                {/if}
            {/if}
            {if $pagedesign.data_map.site_map_url.data_text|ne('')}
                {if $pagedesign.data_map.site_map_url.content|eq('')}
                <li><a href={concat("/content/view/sitemap/", $indexpage)|ezurl} title="{$pagedesign.data_map.site_map_url.data_text|wash}">{$pagedesign.data_map.site_map_url.data_text|wash}</a></li>
                {else}
                <li><a href={$pagedesign.data_map.site_map_url.content|ezurl} title="{$pagedesign.data_map.site_map_url.data_text|wash}">{$pagedesign.data_map.site_map_url.data_text|wash}</a></li>
                {/if}
            {/if}
            {if $basket_is_empty|not()}
            <li><a href={"/shop/basket/"|ezurl} title="{$pagedesign.data_map.shopping_basket_label.data_text|wash}">{$pagedesign.data_map.shopping_basket_label.data_text|wash}</a></li>
           {/if}
        {if $current_user.is_logged_in}
            {if $pagedesign.data_map.my_profile_label.has_content}
            <li><a href={concat( "/user/edit/", $current_user.contentobject_id )|ezurl} title="{$pagedesign.data_map.my_profile_label.data_text|wash}">{$pagedesign.data_map.my_profile_label.data_text|wash}</a></li>
            {/if}
            {if $pagedesign.data_map.logout_label.has_content}
            <li><a href={"/user/logout"|ezurl} title="{$pagedesign.data_map.logout_label.data_text|wash}">{$pagedesign.data_map.logout_label.data_text|wash} ( {$current_user.contentobject.name|wash} )</a></li>
            {/if}
        {else}
            {if $pagedesign.data_map.register_user_label.has_content}
            <li><a href={"/user/register"|ezurl} title="{$pagedesign.data_map.register_user_label.data_text|wash}">{$pagedesign.data_map.register_user_label.data_text|wash}</a></li>
            {/if}
            {if $pagedesign.data_map.login_label.has_content}
            <li><a href={"/user/login"|ezurl} title="{$pagedesign.data_map.login_label.data_text|wash}">{$pagedesign.data_map.login_label.data_text|wash}</a></li>
            {/if}
        {/if}

        {if $pagedesign.can_edit}
            <li><a href={concat( "/content/edit/", $pagedesign.id, "/f/", ezini( 'RegionalSettings', 'Locale' , 'site.ini'), "/", $pagedesign.initial_language_code )|ezurl} title="{$pagedesign.data_map.site_settings_label.data_text|wash}">{$pagedesign.data_map.site_settings_label.data_text|wash}</a></li>
        {/if}
        </ul>
    </div>
    </div>
  {cache-block keys=array( $uri_string, $user_hash )}
    <div id="logo">
    {if $pagedesign.data_map.image.content.is_valid|not()}
        <h1><a href={"/"|ezurl} title="{ezini('SiteSettings','SiteName')}">{ezini('SiteSettings','SiteName')}</a></h1>
    {else}
        <a href={"/"|ezurl} title="{ezini('SiteSettings','SiteName')}"><img src={$pagedesign.data_map.image.content[logo].full_path|ezroot} alt="{$pagedesign.data_map.image.content[logo].text}" width="{$pagedesign.data_map.image.content[logo].width}" height="{$pagedesign.data_map.image.content[logo].height}" /></a>
    {/if}
    </div>
    <div id="searchbox">
      <form action={"/content/search"|ezurl}>
        <label for="searchtext" class="hide">Search text:</label>
        {if eq( $ui_context, 'edit' )}
        <input id="searchtext" name="SearchText" type="text" value="" size="12" disabled="disabled" />
        <input id="searchbutton" class="button-disabled" type="submit" value="{'Search'|i18n('design/ezwebin/pagelayout')}" alt="Submit" disabled="disabled" />
        {else}
        <input id="searchtext" name="SearchText" type="text" value="" size="12" />
        <input id="searchbutton" class="button" type="submit" value="{'Search'|i18n('design/ezwebin/pagelayout')}" alt="Submit" />
            {if eq( $ui_context, 'browse' )}
             <input name="Mode" type="hidden" value="browse" />
            {/if}
        {/if}
      </form>
    </div>
    <p class="hide"><a href="#main">Skip to main content</a></p>
  </div>
  <!-- Header area: END -->


  <!-- Top menu area: START -->
  <div id="topmenu" class="float-break">
    {include uri='design:menu/flat_top.tpl'}
  </div>
  <!-- Top menu area: END -->
  {if not( and( is_set( $content_info.class_identifier ),
                eq( $content_info.class_identifier, 'frontpage' ),
	        and( is_set( $content_info.viewmode ), ne( $content_info.viewmode, 'sitemap' ) ),
	        and( is_set( $content_info.viewmode ), ne( $content_info.viewmode, 'tagcloud' ) )
	      )
         )}

  <!-- Path area: START -->
  <div id="path">
    {include uri='design:parts/path.tpl'}
  </div>
  <!-- Path area: END -->
  {/if}


  <!-- Toolbar area: START -->
  <div id="toolbar">
  {if and( $current_node_id,
           $current_user.is_logged_in,
           and( is_set( $content_info.viewmode ), ne( $content_info.viewmode, 'sitemap' ) ),
           and( is_set( $content_info.viewmode ), ne( $content_info.viewmode, 'tagcloud' ) ) ) }
  {include uri='design:parts/website_toolbar.tpl'}
  {/if}
  </div>
  <!-- Toolbar area: END -->


  <!-- Columns area: START -->
  <div id="columns" class="float-break">
    <!-- Side menu area: START -->
    <div id="sidemenu-position">
      <div id="sidemenu">
          <!-- Used only for height resize script -->
          {if and($current_node_id, gt($module_result.path|count, $pagerootdepth))}
          {include uri='design:menu/flat_left.tpl'}
          {/if}
       </div>
    </div>
    <!-- Side menu area: END -->
  {/cache-block}
{/cache-block}
    <!-- Main area: START -->
    <div id="main-position">
      <div id="main" class="float-break">
        <div class="overflow-fix">
          <!-- Main area content: START -->
          {$module_result.content}
          <!-- Main area content: END -->
        </div>
      </div>
    </div>
    <!-- Main area: END -->
{cache-block keys=array($uri_string, $user_hash, $access_type.name)}
    <!-- Extra area: START -->
    <div id="extrainfo-position">
      <div id="extrainfo">
          <!-- Extra content: START -->
          {if $current_node_id}
            {include uri='design:parts/extra_info.tpl'}
          {/if}
          <!-- Extra content: END -->
      </div>
    </div>
    <!-- Extra area: END -->

  </div>
  <!-- Columns area: END -->

  {if is_unset($pagedesign)}
      {if is_unset($pagedesign_class)}
          {def $pagedesign_class = fetch( 'content', 'class', hash( 'class_id', 'template_look' ) )}
      {/if}
      {if $pagedesign_class.object_count|gt( 0 )}
          {def $pagedesign = $pagedesign_class.object_list[0]}
      {/if}
  {/if}

{include uri='design:page_footer.tpl'}

</div>
<!-- Complete page area: END -->

{if $pagedesign.data_map.footer_script.has_content}
<script language="javascript" type="text/javascript">
<!--

    {$pagedesign.data_map.footer_script.content}

-->
</script>
{/if}
{/cache-block}

{* This comment will be replaced with actual debug report (if debug is on). *}
<!--DEBUG_REPORT-->
</body>
</html>
