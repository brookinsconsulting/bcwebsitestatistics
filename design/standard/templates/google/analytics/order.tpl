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

{*
Statement: Field {$order.data_text_1} Stores user information in xml format!
Question: Patch kernel or impliment phpoperator to explode and return key user xml entries required?
Dependancies:

{def $user_city=$order.data_text_1.city}
{def $user_state=$order.data_text_1.state}
{def $user_country=$order.data_text_1.country}
{$order.data_text_1}<hr />
*}

{def $user_city=wrap_user_func('getXMLString', array('city', $order.data_text_1) )}
{def $user_state=wrap_user_func('getXMLString', array('state', $order.data_text_1) )}
{def $user_country=wrap_user_func('getXMLString', array('country', $order.data_text_1))}
{def $shopname=ezini('GoogleAnalyticsWorkflow', 'ShopName', 'googleanalytics.ini')}

{*
Format:
    UTM:T|[order-id]|[affiliation]|
    [total]|[tax]| [shipping]|[city]|[state]|[country] UTM:I|[order-id]|[sku/code]|[productname]|[category]|[price]|
    [quantity]
*}
    <form style="display:none;" name="utmform">
{def $np_order_subtotal_price_ex_vat=wrap_user_func('getFormatNumericDecimal', $order.total_ex_vat )
     $np_order_subtotal_price_vat_sub=sub($order.total_inc_vat,$order.total_ex_vat)
     $np_order_subtotal_price_vat=wrap_user_func('getFormatNumericDecimal', $np_order_subtotal_price_vat_sub)
}
    <textarea id="utmtrans">UTM:T|{$order.order_nr}|{$shopname|wash}|{$np_order_subtotal_price_ex_vat}|{$np_order_subtotal_price_vat}|{section name=OrderItem loop=$order.order_items show=$order.order_items sequence=array(bglight,bgdark)}{def $np_order_item_price_inc_vat=wrap_user_func('getFormatNumericDecimal', $OrderItem:item.price_inc_vat )}{$np_order_item_price_inc_vat}{/section}|{$user_city}|{$user_state}|{$user_country}
{section var=product_item loop=$order.product_items sequence=array(bglight,bgdark)}
{def $np_product_item_price_inc_vat=$product_item.price_inc_vat}

UTM:I|{$order.order_nr}|{$product_item.node_id}|{$product_item.object_name}|{$product_item.item_object.contentobject.main_node.parent.name}|{$np_product_item_price_inc_vat}|{$product_item.item_count}
{/section}</textarea>
    </form>
</div>
{undef}
