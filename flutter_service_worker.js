'use strict';
const MANIFEST = 'flutter-app-manifest';
const TEMP = 'flutter-temp-cache';
const CACHE_NAME = 'flutter-app-cache';

const RESOURCES = {"assets/AssetManifest.bin": "55b5f2a8d81ce443c126c1e68213a315",
"assets/AssetManifest.bin.json": "a266a6ae9783c4504a1b29cc853ab94a",
"assets/AssetManifest.json": "b69cdf8dcf4963c19e1fca666ca7f0d5",
"assets/assets/fonts/NotoSansSC-Black.ttf": "59d0ace34b5eec93cda32bf190477387",
"assets/assets/fonts/NotoSansSC-Bold.ttf": "f8f91dd976cfe63e46490e63345e8c2e",
"assets/assets/fonts/NotoSansSC-Medium.ttf": "e8419f3e73dfc6e87a23bc4622ee587a",
"assets/assets/fonts/NotoSansSC-SemiBold.ttf": "0918ca4766af7cdb165d2d477d9ae5d0",
"assets/assets/fonts/OpenSans-Bold.ttf": "0a191f83602623628320f3d3c667a276",
"assets/assets/fonts/OpenSans-ExtraBold.ttf": "f0af8434e183f500acf62135a577c739",
"assets/assets/fonts/OpenSans-Regular.ttf": "931aebd37b54b3e5df2fedfce1432d52",
"assets/assets/fonts/OpenSans-SemiBold.ttf": "e2ca235bf1ddc5b7a350199cf818c9c8",
"assets/assets/images/about.png": "eff2f8f25e1f4670eac817f5c5a26e13",
"assets/assets/images/back%2520arrow%2520-%2520white.png": "47236beb942097455db010745e783972",
"assets/assets/images/back%2520arrow.png": "81d6d43fbf4f2c5307f41efd9ee81c94",
"assets/assets/images/background.png": "d453d34fba1e5258aef4303d81871965",
"assets/assets/images/background_fade.png": "d0905b688b702f8961590d1eb637e97d",
"assets/assets/images/background_header%2520-%2520demo.png": "6ae88945a3b13228a0e4338c7a3cdaa0",
"assets/assets/images/background_header.png": "c3d7d391bfd16e0acf142e8ac68c52e8",
"assets/assets/images/btn_close.png": "7e2eeb0bf87a1b65669bc0b4eff5d098",
"assets/assets/images/btn_gradient.png": "8d434959fc06b7a1e978d9f996a07b7f",
"assets/assets/images/btn_gradient_disable.png": "03ac4cf496fc5f49c3404b95362216c8",
"assets/assets/images/commitee.png": "006c8199611c6211af3825122f4cd635",
"assets/assets/images/commitee_placeholder.png": "cec54cd63abe440c9ef20490caa5c6cc",
"assets/assets/images/commitee_victor.png": "fdda5c03fdeba85344d14b36264ad201",
"assets/assets/images/email.png": "fe9b46b38362acf688b5c9894ac4a715",
"assets/assets/images/event_mockup1.png": "fec72012df3b87d2ebee45a8ebd70714",
"assets/assets/images/event_mockup2.png": "708d1dc6be999ff405cddae970e1c985",
"assets/assets/images/home_banner.png": "6753bd336af30fa87f4be34744e7d108",
"assets/assets/images/home_mock_ev0.png": "1e8be895f22ef7005cb8657b5517a80f",
"assets/assets/images/home_mock_ev1.png": "0b111c1f262a5f2828d5d516e9434b51",
"assets/assets/images/icon_account.png": "466ad39ec591fe3112f346ca03b5c145",
"assets/assets/images/icon_arrow_goto%2520-%2520disable.png": "9ab27addab8527617ecf13fa0c5c3c17",
"assets/assets/images/icon_arrow_goto.png": "266a115c6bfd3d114f25214a7064a995",
"assets/assets/images/icon_arrow_more.png": "b15597f2e43a87c56366d68489934765",
"assets/assets/images/icon_BacktoTop.png": "7f9bb18e85cec4b5b87d820fd0de4550",
"assets/assets/images/icon_biz%2520connect.png": "1f37fc12e874e34d1e4e4d1ace1045c9",
"assets/assets/images/icon_correct.png": "ffb73a91f799ebedcd0a90ee2a035e28",
"assets/assets/images/icon_event.png": "2a772439de008b5cb64f0cf0ffa7b789",
"assets/assets/images/icon_hidepass.png": "0f0fb114aa97cec53f6049932becfd8a",
"assets/assets/images/icon_home.png": "23f6e33ec379f80ee3ef12cb8a827462",
"assets/assets/images/icon_lock.png": "ca6bf21330f71c4837e86927981efca8",
"assets/assets/images/icon_logout.png": "e2895456d66a8cfec62094c80f692742",
"assets/assets/images/icon_mail.png": "eb1c69171c943d210ee5bc9097d1f5b6",
"assets/assets/images/icon_none.png": "a2b6af1e85887b316d4c95d63d9c9f51",
"assets/assets/images/icon_showpass.png": "b0b6d60c3f8f020e344973793694cf10",
"assets/assets/images/Introduction1.png": "d78dda0c01b692f4a876f6c92f06bd75",
"assets/assets/images/Introduction2.png": "a0bc40af4507ae1a9a2eecd297e4bc64",
"assets/assets/images/Introduction3.png": "a261797d7e9fa8d6d2c4e804539da47e",
"assets/assets/images/Introduction4.png": "f9f7c777b50f1c2d03443544cace0b75",
"assets/assets/images/landing_img1.png": "2eae609e03dc3715a1ddad813bd02653",
"assets/assets/images/landing_img2.png": "87f81cf6fe63563b990f67c29b05c3ad",
"assets/assets/images/landing_img3.png": "31c8c93c385ad14a3733ccf4e6612b49",
"assets/assets/images/noti.png": "3eb9ba0845ac53505ec7a5041c8c8bea",
"assets/assets/images/Rev%2520Logo%2520short.png": "b141064ff27366fa2a701147f35e7a3d",
"assets/assets/images/Rev%2520Logo.png": "648169391096318d26d7322d64528960",
"assets/assets/images/Union.png": "61862815ee2ed5facfaf7319fe87498e",
"assets/assets/images/WIP.png": "c0daad72d794b3d367cd40efb26b1633",
"assets/FontManifest.json": "96c1198c439367961c68fc1266dd318d",
"assets/fonts/MaterialIcons-Regular.otf": "0db35ae7a415370b89e807027510caf0",
"assets/NOTICES": "9f9d6e1133a4173ca78443fe2efc6c8a",
"assets/packages/cupertino_icons/assets/CupertinoIcons.ttf": "e986ebe42ef785b27164c36a9abc7818",
"assets/shaders/ink_sparkle.frag": "ecc85a2e95f5e9f53123dcaf8cb9b6ce",
"canvaskit/canvaskit.js": "5fda3f1af7d6433d53b24083e2219fa0",
"canvaskit/canvaskit.js.symbols": "48c83a2ce573d9692e8d970e288d75f7",
"canvaskit/canvaskit.wasm": "1f237a213d7370cf95f443d896176460",
"canvaskit/chromium/canvaskit.js": "87325e67bf77a9b483250e1fb1b54677",
"canvaskit/chromium/canvaskit.js.symbols": "a012ed99ccba193cf96bb2643003f6fc",
"canvaskit/chromium/canvaskit.wasm": "b1ac05b29c127d86df4bcfbf50dd902a",
"canvaskit/skwasm.js": "9fa2ffe90a40d062dd2343c7b84caf01",
"canvaskit/skwasm.js.symbols": "262f4827a1317abb59d71d6c587a93e2",
"canvaskit/skwasm.wasm": "9f0c0c02b82a910d12ce0543ec130e60",
"canvaskit/skwasm.worker.js": "bfb704a6c714a75da9ef320991e88b03",
"favicon.png": "b08399c8df9a34bb2d69b78ebc8c766b",
"flutter.js": "f31737fb005cd3a3c6bd9355efd33061",
"flutter_bootstrap.js": "a9504cde3d996979d291a528a6057fbe",
"icons/Icon-192.png": "855c7a411f9193c72b206fca84cfe404",
"icons/Icon-512.png": "1aba06056ad966c28dcc761f30b784a9",
"icons/Icon-maskable-192.png": "855c7a411f9193c72b206fca84cfe404",
"icons/Icon-maskable-512.png": "1aba06056ad966c28dcc761f30b784a9",
"index.html": "0844e4d916532058f61b128eb6ca66bd",
"/": "0844e4d916532058f61b128eb6ca66bd",
"lib/submit.php": "0950654d184e94ed2f70376bf4a7beb1",
"loading.gif": "f61742f8d4363b8c03d9b5200794fd66",
"main.dart.js": "1315cf3b0832abf30c0d28da6169371d",
"manifest.json": "cff6529220096bc5b8d7b99749501c89",
"version.json": "497aae6ac16aaf0984cdc2bb618c0583"};
// The application shell files that are downloaded before a service worker can
// start.
const CORE = ["main.dart.js",
"index.html",
"flutter_bootstrap.js",
"assets/AssetManifest.bin.json",
"assets/FontManifest.json"];

// During install, the TEMP cache is populated with the application shell files.
self.addEventListener("install", (event) => {
  self.skipWaiting();
  return event.waitUntil(
    caches.open(TEMP).then((cache) => {
      return cache.addAll(
        CORE.map((value) => new Request(value, {'cache': 'reload'})));
    })
  );
});
// During activate, the cache is populated with the temp files downloaded in
// install. If this service worker is upgrading from one with a saved
// MANIFEST, then use this to retain unchanged resource files.
self.addEventListener("activate", function(event) {
  return event.waitUntil(async function() {
    try {
      var contentCache = await caches.open(CACHE_NAME);
      var tempCache = await caches.open(TEMP);
      var manifestCache = await caches.open(MANIFEST);
      var manifest = await manifestCache.match('manifest');
      // When there is no prior manifest, clear the entire cache.
      if (!manifest) {
        await caches.delete(CACHE_NAME);
        contentCache = await caches.open(CACHE_NAME);
        for (var request of await tempCache.keys()) {
          var response = await tempCache.match(request);
          await contentCache.put(request, response);
        }
        await caches.delete(TEMP);
        // Save the manifest to make future upgrades efficient.
        await manifestCache.put('manifest', new Response(JSON.stringify(RESOURCES)));
        // Claim client to enable caching on first launch
        self.clients.claim();
        return;
      }
      var oldManifest = await manifest.json();
      var origin = self.location.origin;
      for (var request of await contentCache.keys()) {
        var key = request.url.substring(origin.length + 1);
        if (key == "") {
          key = "/";
        }
        // If a resource from the old manifest is not in the new cache, or if
        // the MD5 sum has changed, delete it. Otherwise the resource is left
        // in the cache and can be reused by the new service worker.
        if (!RESOURCES[key] || RESOURCES[key] != oldManifest[key]) {
          await contentCache.delete(request);
        }
      }
      // Populate the cache with the app shell TEMP files, potentially overwriting
      // cache files preserved above.
      for (var request of await tempCache.keys()) {
        var response = await tempCache.match(request);
        await contentCache.put(request, response);
      }
      await caches.delete(TEMP);
      // Save the manifest to make future upgrades efficient.
      await manifestCache.put('manifest', new Response(JSON.stringify(RESOURCES)));
      // Claim client to enable caching on first launch
      self.clients.claim();
      return;
    } catch (err) {
      // On an unhandled exception the state of the cache cannot be guaranteed.
      console.error('Failed to upgrade service worker: ' + err);
      await caches.delete(CACHE_NAME);
      await caches.delete(TEMP);
      await caches.delete(MANIFEST);
    }
  }());
});
// The fetch handler redirects requests for RESOURCE files to the service
// worker cache.
self.addEventListener("fetch", (event) => {
  if (event.request.method !== 'GET') {
    return;
  }
  var origin = self.location.origin;
  var key = event.request.url.substring(origin.length + 1);
  // Redirect URLs to the index.html
  if (key.indexOf('?v=') != -1) {
    key = key.split('?v=')[0];
  }
  if (event.request.url == origin || event.request.url.startsWith(origin + '/#') || key == '') {
    key = '/';
  }
  // If the URL is not the RESOURCE list then return to signal that the
  // browser should take over.
  if (!RESOURCES[key]) {
    return;
  }
  // If the URL is the index.html, perform an online-first request.
  if (key == '/') {
    return onlineFirst(event);
  }
  event.respondWith(caches.open(CACHE_NAME)
    .then((cache) =>  {
      return cache.match(event.request).then((response) => {
        // Either respond with the cached resource, or perform a fetch and
        // lazily populate the cache only if the resource was successfully fetched.
        return response || fetch(event.request).then((response) => {
          if (response && Boolean(response.ok)) {
            cache.put(event.request, response.clone());
          }
          return response;
        });
      })
    })
  );
});
self.addEventListener('message', (event) => {
  // SkipWaiting can be used to immediately activate a waiting service worker.
  // This will also require a page refresh triggered by the main worker.
  if (event.data === 'skipWaiting') {
    self.skipWaiting();
    return;
  }
  if (event.data === 'downloadOffline') {
    downloadOffline();
    return;
  }
});
// Download offline will check the RESOURCES for all files not in the cache
// and populate them.
async function downloadOffline() {
  var resources = [];
  var contentCache = await caches.open(CACHE_NAME);
  var currentContent = {};
  for (var request of await contentCache.keys()) {
    var key = request.url.substring(origin.length + 1);
    if (key == "") {
      key = "/";
    }
    currentContent[key] = true;
  }
  for (var resourceKey of Object.keys(RESOURCES)) {
    if (!currentContent[resourceKey]) {
      resources.push(resourceKey);
    }
  }
  return contentCache.addAll(resources);
}
// Attempt to download the resource online before falling back to
// the offline cache.
function onlineFirst(event) {
  return event.respondWith(
    fetch(event.request).then((response) => {
      return caches.open(CACHE_NAME).then((cache) => {
        cache.put(event.request, response.clone());
        return response;
      });
    }).catch((error) => {
      return caches.open(CACHE_NAME).then((cache) => {
        return cache.match(event.request).then((response) => {
          if (response != null) {
            return response;
          }
          throw error;
        });
      });
    })
  );
}
