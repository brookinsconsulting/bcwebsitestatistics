<div class="shop-basket">
    <h2>{"Order Completed #%order_id "|i18n("design/base/shop",,
         hash( '%order_id', $order.order_nr,
               '%order_status', $order.status_name ) )}</h2>
    <br />
    <div style="width: 100%;  color: #565969; font-size: 14px; font-weight: bold; background-color: #EBEEEF; float: left; padding-top: 5px; padding-bottom: 5px; padding-left: 5px;">
    <div style="float: left;">1. Shopping Cart</div>
    <div style="float: left; padding-left: 40px;">2. Billing &amp; Shipping</div>
    <div style="float: left; padding-left: 40px;">3. Confirmation</div>
    <div style="float: left; padding-left: 40px;">4. Payment info</div>
    <div style="float: left; padding-left: 40px; color: #308d9d;">5. Order Completed</div>
    <div style="float: left; padding-left: 40px;">6. Review order receipt</div>
    </div>
    <div class="break"></div>
    <br />
    <form method="post" name="basket" action={"/shop/checkout"|ezurl}>
    <input type="hidden" name="validate" value="validate" />
    <div align="right"><input type="image" name="StoreChangesButton" value="Store" src={"images/continue.gif"|ezdesign()} /></div>
    </form>

    {shop_account_view_gui view=html order=$order}
    {def $currency = fetch( 'shop', 'currency', hash( 'code', $order.productcollection.currency_code ) )
         $locale = false()
         $symbol = false()}
    {if $currency}
        {set locale = $currency.locale
             symbol = $currency.symbol}
    {/if}
    <div class="content-basket">
    <table cellspacing="0" border="0">
    <tr>
    <th>
        {"Quantity"|i18n("design/base/shop")}
        </th>
        <th>
        {"Item"|i18n("design/base/shop")}
        </th>
        <th align="right">
        {"TAX"|i18n("design/base/shop")}
        </th>
        <th align="right">
    	{"Price"|i18n("design/base/shop")}
        </th>
        <th align="right">
	    {"Discount"|i18n("design/base/shop")}
        </th>
        <th align="right">
     	{"Total Price"|i18n("design/base/shop")}
        </th>
    </tr>
    {section var=product_item loop=$order.product_items sequence=array(bglight,bgdark)}
    <tr>
        <td class="{$product_item.sequence} product-name" align="center" valign="top">
            {$product_item.item_count}
    	</td>
    	<td class="{$product_item.sequence} product-name">
        <a href={concat("/content/view/full/",$product_item.node_id,"/")|ezurl}>{$product_item.object_name}</a>
        {section show=$product_item.item.item_object.option_list}
              <table class="shop-option_list">
         {section var=option_item loop=$product_item.item_object.option_list}
         <tr>
         {*<td class="shop-option_name">{$option_item.name}</td>*}
             <td class="shop-option_value">
                 {def $vary=$product_item.item_object.contentobject.data_map.variation.content.option_list[$product_item.item_object.option_list.0.option_item_id]}
                 {$option_item.value}<br />
                 <b>{$vary.comment}</b><br />
                 {if or(ne($vary.weight, false()), ne($vary.weight, "0"))}Weight:{$vary.weight} lbs</b><br />{/if}
             </td>
         </tr>
         {/section}
         </table>
         {section-else}
             <table class="shop-option_list">
                 <tr>
                    {def $prod=fetch( 'content', 'node', hash( 'node_id', $product_item.node_id ) )}
                     <td class="shop-option_value">{$prod.data_map.product_id.content}</td>
                 </tr>
            </table>
         {/section}
        </td>
	    <td align="right" valign="top">
        {$product_item.vat_value} %
    	</td>
        <td align="right" valign="top">
        {$product_item.price_inc_vat|l10n( 'currency', $locale, $symbol )}
    	</td>
	    <td align="right" valign="top">
        {$product_item.discount_percent}%
        </td>
	    <td align="right" valign="top">
        {$product_item.total_price_inc_vat|l10n( 'currency', $locale, $symbol )}
	    </td>
     </tr>
     <tr><td colspan="6"><hr /></tr>
     {/section}
     <tr>
         <td colspan='6' align="right">
         Subtotal Inc. TAX:
         <strong>{$order.product_total_inc_vat|l10n( 'currency', $locale, $symbol )}</strong>
         </td>
     </tr>
     	<tr><td colspan="6"><hr /></tr>
     </table>
     </div>

    <h2>{"Order summary"|i18n("design/base/shop")}:</h2>
    <table class="list" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td class="bgdark">
        {"Subtotal of items"|i18n("design/base/shop")}:
        </td>
        <td class="bgdark">
        {$order.product_total_inc_vat|l10n( 'currency', $locale, $symbol )}
        </td>
    </tr>
    {section name=OrderItem loop=$order.order_items show=$order.order_items sequence=array(bglight,bgdark)}
    <tr>
        <td class="{$OrderItem:sequence}">
        {$OrderItem:item.description}:
    	</td>
        <td class="{$OrderItem:sequence}">
        {$OrderItem:item.price_inc_vat|l10n( 'currency', $locale, $symbol )}
    	</td>
    </tr>
    {/section}
    <tr>
        <td class="bgdark">
        <b>{"Order total"|i18n("design/base/shop")}</b>
        </td>
        <td class="bgdark">
        <b>{$order.total_ex_vat|l10n( 'currency', $locale, $symbol )}</b>
        </td>
        <td class="bgdark">
        <b>{$order.total_inc_vat|l10n( 'currency', $locale, $symbol )}</b>
        </td>
    </tr>
    </table>

    {'false'|bc_ga_urchinOrder( $order )}
</div>
{undef}
