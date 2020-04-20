/**
 * ShopifySync plugin for Craft CMS
 *
 * Index Field JS
 *
 * @author    Fatfish
 * @copyright Copyright (c) 2020 Fatfish
 * @link      www.fatfish.com.au
 * @package   ShopifySync
 * @since     1.0.0
 */
var app = new Vue({

    el:'#app',
    data:{
        counter:''
    },
    methods:{
        FetchShopifyProducts:function (e) {

            this.$http.post("https://www.google.com");
            
        }
    }

});