/**
 * ShopifySync plugin for Craft CMS
 *
 * ShopifySync JS
 *
 * @author    Fatfish
 * @copyright Copyright (c) 2020 Fatfish
 * @link      www.fatfish.com.au
 * @package   ShopifySync
 * @since     1.0.0
 */



$("#fetch_product_btn").on('click',function(e)
{
    e.preventDefault();
    $("#fetch_product_btn").val("In Progress ...")
    $("#fetch_product_btn").attr("disabled",true);
    $("#pbar").css('opacity',1);
    $("#pmessage").css("display",'block');



    Craft.postActionRequest('/admin/fetchproduct',function (response,status) {

        if (status == "success")
        {
            var message='';
            $("#fetch_product_btn").attr("disabled",false);
            $("#fetch_product_btn").val("Import Products");
            $("#pbar").css('opacity',0);
            $("#pmessage").html("Completed");

            $.each(response,function(key,value){

              message+= "<tr>";
              message+= "<td>"+value.ProductName+"</td>";
              message+= "<td>"+value.message+"</td>";
              message+= "<td>"+value.ProductImport+"</td>";
              message+= "</tr>";


            });
            $(".productbody").html(message);
        }else
        {

        }
    });

});

