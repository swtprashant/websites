$(document).ready(function () {
    showPaidCtn('mensxp_hp_rcmw_rhs_ctn_nat',1);
	showCTNAd('card_hot_3',1);
    showCTNAd('card_hot_9',2);
    showCTNAd('card_hot_15',3);
	showCTNAd('card_technology_3',4);
	showCTNAd('card_relationships_3',5);
	showCTNAd('card_health_3',6);
	showCTNAd('card_fashion_3',7);
	showCTNAd('card_grooming_3',8);
	showCTNAd('card_work-life_3',9);
	showLatestRhs('cls_4',1);
});

$(document).on('mouseenter', '.colombia-logo-gp', function() {

    $(this).find( ".colombia-logo" ).attr("src","http://static.clmbtech.com/ad/commons/images/colombia_red_small.png");
});

$(document).on('mouseleave', '.colombia-logo-gp', function() {

    $(this).find( ".colombia-logo" ).attr("src","http://static.clmbtech.com/ad/commons/images/colombia-icon.png");
});

function showCTNAd(divClass,position){	

	var divId=$("."+divClass).attr('id');	
	$('#'+divId).attr( "data-slot", "129207" );
	$('#'+divId).attr( "data-position", position );
	$('#'+divId).attr( "data-section", "0" );
	$('#'+divId).attr( "data-cb", "HpCallBack" );
	$('#'+divId).addClass("colombia");
}

function HpCallBack(data,container)
{
    if(data==null){
        ga('send', 'event', 'LHS_SectionPage_Data', 'Fail', container);
		var node=$('#'+container);
			if(node.hasClass( "colombiaFail" ))
			{
				node.show();
			}
    }else{
        ga('send', 'event', 'LHS_SectionPage_Data', 'Success', container);
    }
    var mainImage = data. paidAds[0]. mainimage;
    if(mainImage==null){
        ga('send', 'event', 'LHS_SectionPage_image', 'Fail', container);
    }else{
        ga('send', 'event', 'LHS_SectionPage_image', 'Success', container);
    }
	
	if((data==null)||(data.paidAds[0].mainimage==null)||(data.paidAds[0].mainimage=='')||
		(data.paidAds[0].title==null)||(data.paidAds[0].title=='')||
		(data.paidAds[0].clk==null)||(data.paidAds[0].clk==''))
		{
			return;
		}
	
    var title = data. paidAds[0].title;
    var brandtext = data. paidAds[0].brandtext;
    if(data!=null)
    {
        varTpl = '<a target="_blank" href='+data. paidAds[0].clk+' class="tint transAllEase"><img style="transition: all 0.2s ease 0s; display: inline;" src='+data. paidAds[0]. mainimage+' class="transAllEase" ></a><figcaption class="transAllEase"><a target="_blank" href='+data. paidAds[0].clk+' class="lnk">'+data. paidAds[0].title+'</a><span class="by blk colombia-logo-gp"><span class="lc byTxt" style="line-height:24px;">Ad '+data. paidAds[0].brandtext+'</span><span style="float:right; width:10px; height:10px; margin:6px 16px 5px 0"><img class="colombia-logo" src="'+data. paidAds[0].colombiaLogo+'" /></span></span></figcaption>';
        $('#'+container).html(varTpl);
    } 
}

function showLatestRhs(divClass,position){
	var divId=divClass+'-'+(new Date()).getTime();
	$("."+divClass).attr('id', divId);
	$('#'+divId).attr( "data-slot", "207180" );
	$('#'+divId).attr( "data-position", position );
	$('#'+divId).attr( "data-section", "0" );
	$('#'+divId).attr( "data-cb", "LatestRhsCallBack" );
	$('#'+divId).addClass("colombia");
}

function LatestRhsCallBack(data,container)
{
    if(data==null){
        ga('send', 'event', 'LHS_SectionPage_Data', 'Fail', container);
		var node=$('#'+container);
			if(node.hasClass( "colombiaFail" ))
			{
				node.show();
			}
    }else{
        ga('send', 'event', 'LHS_SectionPage_Data', 'Success', container);
    }
    var mainImage = data. paidAds[0]. mainimage;
    if(mainImage==null){
        ga('send', 'event', 'LHS_SectionPage_image', 'Fail', container);
    }else{
        ga('send', 'event', 'LHS_SectionPage_image', 'Success', container);
    }
	
	if((data==null)||(data.paidAds[0].mainimage==null)||(data.paidAds[0].mainimage=='')||
		(data.paidAds[0].title==null)||(data.paidAds[0].title=='')||
		(data.paidAds[0].clk==null)||(data.paidAds[0].clk==''))
		{
			return;
		}
    
    var title = data. paidAds[0].title;
    var brandtext = data. paidAds[0].brandtext;
    if(data!=null)
    {
       var shortTitle = jQuery.trim(title).substring(0, 45).split(" ").slice(0, -1).join(" ") + "...";
        varTpl = '<figure class="innerBlk blk"><a target="_blank"  class="StoryImg rc" title="'+title+'" href='+data. paidAds[0].clk+'><img width="103" height="51" src="'+mainImage+'"></a><a target="_blank" class="StoryLink lc" title="'+title+'" href='+data. paidAds[0].clk+'>'+shortTitle+'</a><span class="colombia-logo-gp" style="display: block; height: 18px; width: 66%; position: absolute; bottom: 0; margin-left: -6px;"><span class="lc byTxt" style="line-height:23px; margin-left:9px;">Ad '+data. paidAds[0].brandtext+'</span><span style="float:right; width:10px; height:10px; margin:6px 16px 5px 0;"><img class="colombia-logo" src="'+data. paidAds[0].colombiaLogo+'" /></span></span></figure>';
        $('#'+container).html(varTpl);
         
    } 
}


function showPaidCtn(divId, position){
	
	$('#'+divId).attr( "data-slot", "129781" );
	$('#'+divId).attr( "data-position", position );
	$('#'+divId).attr( "data-section", "0" );
	$('#'+divId).attr( "data-cb", "HpPaidCallBack" );
	$('#'+divId).addClass("colombia");
}

function HpPaidCallBack(data, container){
 
	url = window.location.href;
	if(data==null){
		var node=$('#'+container);
			if(node.hasClass( "colombiaFail" ))
			{
				node.show();
			}
        ga('send', 'event', 'CTN-Failed', "129781", url);
    }
	
	if((data==null)||(data.paidAds[0].mainimage==null)||(data.paidAds[0].mainimage=='')||
		(data.paidAds[0].title==null)||(data.paidAds[0].title=='')||
		(data.paidAds[0].clk==null)||(data.paidAds[0].clk==''))
		{
			return;
		}
	
	
    if(data!=null){
    	
    	var paidAds = data.paidAds;
    	var varTpl = '<div style="width:300px; margin-bottom:10px">';
			varTpl = '<section class="StoryListing mustRead" style="border-top: 5px solid #333;">';
				varTpl += '<div class="lnkSet">';
					varTpl += '<div class="ttl"> <span>around the web</span> </div>';
				varTpl += ' </div>';    			
				varTpl += '<div class="listingCont ga-entity" id="rhsLatestCont">';
					$.each(data.paidAds , function(index, value){
						varTpl += '<div class="ListingBlock ">';
							varTpl += '<figure class="innerBlk blk">';
							varTpl += '<div class="ctn-right">';
							varTpl += '<a href="'+paidAds[index].clk+'" target="_blank"> <img width="103" height="51" src="'+paidAds[index].mainimage+'" class="StoryLink lc"></a>';
							varTpl += '</div>';
							varTpl += '<div class="ctn-left">';
							varTpl += '<a href="'+paidAds[index].clk+'" class="rc ctn-txt" target="_blank">'+paidAds[index].title+' </a>';
							varTpl += '<span class="rc small-txt" target="_blank">'+paidAds[index].brandtext+' </span>';
							varTpl += '</div>';
							varTpl += '</figure>';
						varTpl += '</div>';
					});				
					varTpl += '<div class="ListingBlock colombia-logo-gp">';
						varTpl += '<figure class="innerBlk blk ctn"><div class="fr"><span class="reco"> Recommended By Colombia</span> <img class="colombia-logo" src="'+paidAds[0].colombiaLogo+'" /> </div></figure>';
					varTpl += '</div>';
				varTpl += '</div>';				
			varTpl += '</section>';
		varTpl += '</div>';
		$('#'+container).html(varTpl);	
		
        if(data.paidAds.length < 4){
        	ga('send', 'event', 'CTN-LessRecord', "129781", url);
        }
    }
    else{
		//alert("null data for slot : 129781");
	}
}


