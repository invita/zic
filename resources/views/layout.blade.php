<!DOCTYPE html>
<html lang="sl" xml:lang="sl" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <link rel="icon" type="image/png" href="/img/icon.png">
    <title>ZIC - Zgodovinski indeks citiranosti</title>

    <script src="/lib/jquery/jquery-2.1.1.js"></script>
    <script src="/lib/jquery-ui-1.12.1/jquery-ui.js"></script>

    <script src="/lib/Chart.min.js"></script>

    <script src="/js/si4/si4.js"></script>
    <script src="/js/si4/config.js"></script>
    <script src="/js/si4/util.js"></script>
    <script src="/js/si4/codes.js"></script>
    <script src="/js/si4/api.js"></script>
    <script src="/js/si4/chart.js"></script>
    <script src="/js/si4/navigation.js"></script>
    <script src="/js/si4/translate/translate.js"></script>
    <script src="/js/si4/object/si4EventBase.js"></script>
    <script src="/js/si4/object/si4FileUploader.js"></script>
    <script src="/js/si4/widget/si4Element.js"></script>
    <script src="/js/si4/widget/si4TabPage.js"></script>
    <script src="/js/si4/widget/si4DataTable.js"></script>
    <script src="/js/si4/widget/si4Panel.js"></script>
    <script src="/js/si4/widget/si4Form.js"></script>
    <script src="/js/si4/widget/si4Input.js"></script>
    <script src="/js/si4/widget/si4InputArray.js"></script>
    <script src="/js/si4/widget/si4MultiSelect.js"></script>
    <script src="/js/si4/widget/si4Dialog.js"></script>
    <script src="/js/si4/widget/si4HtmlTable.js"></script>
    <script src="/js/si4/widget/si4Hint.js"></script>
    <script src="/js/si4/widget/si4AutoComplete.js"></script>
    <script src="/js/si4/modules/index.js"></script>
    <script src="/js/si4/modules/search.js"></script>
    <script src="/js/si4/modules/zic.js"></script>
    <script src="/js/si4/search/autocomplete.js"></script>
    <script src="/js/si4/init.js"></script>

    <script>
        var lang = '<?php echo $lang; ?>';
    </script>

    <!-- Piwik -->
    <!--
    <script type="text/javascript">
      var _paq = _paq || [];
      _paq.push(["setDomains", ["*.zv1.sistory.si"]]);
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
        var u="//sistory.si/piwik/";
        _paq.push(['setTrackerUrl', u+'piwik.php']);
        _paq.push(['setSiteId', 4]);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>
    <noscript><p><img src="//sistory.si/piwik/piwik.php?idsite=4" style="border:0;" alt="" /></p></noscript>
    -->
    <!-- End Piwik Code -->


    <link rel="stylesheet" href="/lib/jquery-ui-1.12.1/jquery-ui.min.css" />
    <link rel="stylesheet" href="/lib/zurb/css/normalize.css" />
    <link rel="stylesheet" href="/lib/zurb/css/foundation.css" />
    <script src="/lib/zurb/js/foundation.min.js"></script>
    <script src="/lib/zurb/js/foundation/foundation.dropdown.js"></script>
    <script>$(document).foundation();</script>

    <link rel="stylesheet" href="/css/gradients.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/css/standard.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/css/si4.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/css/zic.css" />
</head>

<body>

    <main>
        <div id="langSelect">
            <a href="/?lang=sl">Slovensko</a>
            <a href="/?lang=en">English</a>
        </div>

        <div id="searchbar" class="mb25">
            <div class="row">

                <div class="large-2 medium-2 small-12 columns">
                    <a href="/" title="Žrtve 1. svetovne vojne" style="border:0;">
                        <img id="logoImg" alt="Zgodovina Slovenije - SIstory" src="/img/logo-{{$lang}}.png">
                    </a>
                </div>

                <div class="search large-10 medium-10 small-12 columns" style="height: 7em;">
                    <div class="searchTitle">
                        <!-- Desktop tabs -->
                        <div class="show-for-large-up">
                            <div class="row collapse searchInputTabs">
                                <a class="tab translateHtml" data-translateHtml="mainTabs_pub" href="http://www.sistory.si/"></a>
                                <a class="tab translateHtml" data-translateHtml="mainTabs_zrt1" href="http://zv1.sistory.si/?lang={{$lang}}"></a>
                                <a class="tab translateHtml" data-translateHtml="mainTabs_zrt2" href="http://zv2.sistory.si/?lang={{$lang}}"></a>
                                <a class="tab translateHtml" data-translateHtml="mainTabs_pop" href="http://www.sistory.si/popis"></a>
                                <a class="tab active translateHtml" data-translateHtml="mainTabs_zic"></a>
                            </div>
                        </div>

                        <!-- Mobile dropdown -->
                        <div class="show-for-medium-down">
                            <div class="row collapse searchInputTabsMobile">
                                <a class="dropdownArrow translateHtml" href="#" data-dropdown="drop1" data-translateHtml="mainTabs_zic"></a>
                                <ul id="drop1" class="f-dropdown" data-dropdown-content>
                                    <li><a class="translateHtml" data-translateHtml="mainTabs_pub" href="http://www.sistory.si/"></a></li>
                                    <li><a class="translateHtml" data-translateHtml="mainTabs_zrt1" href="http://zv1.sistory.si/?lang={{$lang}}"></a></li>
                                    <li><a class="translateHtml" data-translateHtml="mainTabs_zrt2" href="http://zv2.sistory.si/?lang={{$lang}}"></a></li>
                                    <li><a class="translateHtml" data-translateHtml="mainTabs_pop" href="http://www.sistory.si/popis"></a></li>
                                    <li><a class="active translateHtml" data-translateHtml="mainTabs_zic" href="http://www.sistory.si/zic"></a></li>
                                </ul>
                            </div>
                        </div>

                    </div>
                    <div class="content katSearch active" id="pnlZics">
                        <form id="searchFormZic">
                            <div class="row collapse searchInputRow">
                                <div class="large-11 medium-11 small-10 columns">
                                    <input id="searchInput" name="search" value="{{$q}}" type="text"
                                      class="translatePlaceholder" data-translatePlaceholder="text_searchString">
                                </div>
                                <div class="large-1 medium-1 small-2 columns">
                                    <a onclick="$('#searchFormZic').submit()" id="searchButtonZic" class="postfix button button_small">
                                        <div class="searchicon"></div>
                                    </a>
                                </div>
                            </div>
                        </form>
                        <div class="row collapse searchInputRow" style="margin-top: 5px;">
                            <div class="large-11 medium-11 small-10 columns">&nbsp;</div>
                            <div class="large-1 medium-1 small-2 columns" style="z-index:10;">
                                <a class="nowrap translateHtml" data-translateHtml="text_vsiZic"
                                    onclick="$('#searchInput').val('*'); $('#searchFormZic').submit();"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div id="pageHolder" class="large-12 medium-12 small-12 columns">
                @yield("content")
            </div>
        </div>

        <footer id="bottom">
            <div class="row">
                <div class="large-3 medium-3 columns hide-for-small">
                    <a href="/" title="SIstory Zgodovina Slovenije" style="border:0;">
                        <img id="logoImg1" alt="SIstory Zgodovina Slovenije" src="/img/logo-bw.png" style="border:0px; width: auto;">
                    </a>
                </div>
                <div class="large-5 medium-5 small-12 columns" style="float:left">
                    <div class="row">
                        <?php echo $footerHtml; ?>
                    </div>
                    <hr class="show-for-small-only">
                </div>
            </div>

            <div id="bottomfoot">
                <div class="row">
                    <hr/>
                    <div class="large-9 medium-9 small-12 columns">
                        <p>
                            <span>© Copyright 2011-<?php echo date('Y'); ?> INZ, </span>
                            <span class="translateHtml" data-translateHtml="text_sistory"></span> <?php /* echo $HEAD_TITLE */ ?>
                        </p>
                    </div>
                    <div class="large-3 medium-3 small-12 columns"></div>
                </div>
            </div>
        </footer>

    </main>

</body>
</html>