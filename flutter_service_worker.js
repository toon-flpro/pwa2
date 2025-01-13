'use strict';
const MANIFEST = 'flutter-app-manifest';
const TEMP = 'flutter-temp-cache';
const CACHE_NAME = 'flutter-app-cache';

const RESOURCES = {"assets/AssetManifest.bin": "f66d7d54c1f230e84b3646083d382da0",
"assets/AssetManifest.bin.json": "b6a1dbfb78facde9aebbb9665b46c0c7",
"assets/AssetManifest.json": "814a66b495e242b94393deace94864ca",
"assets/assets/fonts/NotoSansSC-Bold.ttf": "f8f91dd976cfe63e46490e63345e8c2e",
"assets/assets/fonts/OpenSans-Bold.ttf": "0a191f83602623628320f3d3c667a276",
"assets/assets/fonts/OpenSans-ExtraBold.ttf": "f0af8434e183f500acf62135a577c739",
"assets/assets/fonts/OpenSans-Regular.ttf": "931aebd37b54b3e5df2fedfce1432d52",
"assets/assets/fonts/OpenSans-SemiBold.ttf": "e2ca235bf1ddc5b7a350199cf818c9c8",
"assets/assets/images/about.png": "0e9802494de71d3cfc70d7ac6e92d924",
"assets/assets/images/back%2520arrow%2520-%2520white.png": "c9dfd94739a68c6e5433a8120f7bb517",
"assets/assets/images/back%2520arrow.png": "602b180503ad7f836b93459514e93c21",
"assets/assets/images/background.png": "0dda2c70d43da205d8925f5029d7adf6",
"assets/assets/images/background_fade.png": "32544c40edaed3a4e63d488339896645",
"assets/assets/images/background_header%2520-%2520demo.png": "992b37c3e74c17773afd92f54277622b",
"assets/assets/images/background_header.png": "e8af5e801b77cc17d2883cdb94a71392",
"assets/assets/images/btn_close.png": "59a9a64e9a67766b83bb6b0c95926025",
"assets/assets/images/btn_gradient.png": "099487c5fc4af7b60a1a19491a55c1ae",
"assets/assets/images/btn_gradient_disable.png": "410b37b6280b65aa08bc6ac4e2ef146a",
"assets/assets/images/commitee.png": "daf11c1488ff029da5f75424d8ed544d",
"assets/assets/images/commitee_placeholder.png": "d3a89db39bf1739977f5646def08fc0a",
"assets/assets/images/commitee_victor.png": "26b287225de99c98c3eccb3119ef16ba",
"assets/assets/images/email.png": "556ffdbc09667a9ee769092818532580",
"assets/assets/images/event_detail_frame.png": "91e91b796c8bd7310748e3fef822a42f",
"assets/assets/images/event_detail_none.png": "a3821de20537e69a7c3d300235ecf2d0",
"assets/assets/images/icon_account.png": "4d37fd602d6dadb4a0910273aa07e104",
"assets/assets/images/icon_arrow_goto%2520-%2520disable.png": "16cc3759b19486da931021fef9fe416b",
"assets/assets/images/icon_arrow_goto.png": "cea3636d836fac33a2348acd122793e9",
"assets/assets/images/icon_arrow_more.png": "b15597f2e43a87c56366d68489934765",
"assets/assets/images/icon_BacktoTop.png": "7f9bb18e85cec4b5b87d820fd0de4550",
"assets/assets/images/icon_biz%2520connect.png": "1f37fc12e874e34d1e4e4d1ace1045c9",
"assets/assets/images/icon_calendar.png": "7f5f562f9ce567e756f0fa9b9aae519b",
"assets/assets/images/icon_calendar_w.png": "34dfa3296b4a6ba328fc6543e62fb33f",
"assets/assets/images/icon_correct.png": "ffb73a91f799ebedcd0a90ee2a035e28",
"assets/assets/images/icon_event.png": "2a772439de008b5cb64f0cf0ffa7b789",
"assets/assets/images/icon_hidepass.png": "0f0fb114aa97cec53f6049932becfd8a",
"assets/assets/images/icon_home.png": "23f6e33ec379f80ee3ef12cb8a827462",
"assets/assets/images/icon_lock.png": "ca6bf21330f71c4837e86927981efca8",
"assets/assets/images/icon_logout.png": "e2895456d66a8cfec62094c80f692742",
"assets/assets/images/icon_mail.png": "eb1c69171c943d210ee5bc9097d1f5b6",
"assets/assets/images/icon_mini_correct.png": "c8c42de1acf8990c5eae0783e68e8485",
"assets/assets/images/icon_mini_wrong.png": "afd9bc6c854d95541454ada0ef9143ea",
"assets/assets/images/icon_none.png": "a2b6af1e85887b316d4c95d63d9c9f51",
"assets/assets/images/icon_pin.png": "64676184cda22060d075f44dee50045f",
"assets/assets/images/icon_pin_w.png": "70fab53e36476601f34e5feeb7bc8de9",
"assets/assets/images/icon_showpass.png": "b0b6d60c3f8f020e344973793694cf10",
"assets/assets/images/icon_square_date.png": "55422a8d6dc20841a3a01ee22fb17679",
"assets/assets/images/icon_square_location.png": "4f194807f17e8ea54caa088b91529372",
"assets/assets/images/Introduction1.png": "d78dda0c01b692f4a876f6c92f06bd75",
"assets/assets/images/Introduction2.png": "a0bc40af4507ae1a9a2eecd297e4bc64",
"assets/assets/images/Introduction3.png": "2ecc9a0c17303181d3f6b5472a595b7d",
"assets/assets/images/Introduction4.png": "eaae86f70c952ce6f1712a210a9a4313",
"assets/assets/images/landing_img1.png": "a77861c48c126c4b2b76898881d25c47",
"assets/assets/images/landing_img2.png": "87f81cf6fe63563b990f67c29b05c3ad",
"assets/assets/images/landing_img3.png": "31c8c93c385ad14a3733ccf4e6612b49",
"assets/assets/images/Language.png": "61862815ee2ed5facfaf7319fe87498e",
"assets/assets/images/noti.png": "3eb9ba0845ac53505ec7a5041c8c8bea",
"assets/assets/images/PageResetPassword.png": "138f4cf812c057081c308de6d47c0bbf",
"assets/assets/images/PageResetPasswordChanged.png": "8afdce3a6bd2a714be4deafc7a56ef4b",
"assets/assets/images/PageResetPasswordCheckInbox.png": "33e3580591ebe8c50acea4a0ca29bdde",
"assets/assets/images/Rev%2520Logo%2520short.png": "b141064ff27366fa2a701147f35e7a3d",
"assets/assets/images/Rev%2520Logo.png": "648169391096318d26d7322d64528960",
"assets/assets/images/tmp_logo.png": "03cbfcbc82133c162ee44b9eb3fae418",
"assets/assets/images/WIP.png": "8f69eeb48cfadc380024fb719546dbbd",
"assets/FontManifest.json": "ea671351312435ad946c08a78d56e7dc",
"assets/fonts/MaterialIcons-Regular.otf": "066eb6051da2641f9dbe35b373400908",
"assets/NOTICES": "20b380b0ddcebbca11db765710442952",
"assets/packages/cupertino_icons/assets/CupertinoIcons.ttf": "e986ebe42ef785b27164c36a9abc7818",
"assets/shaders/ink_sparkle.frag": "ecc85a2e95f5e9f53123dcaf8cb9b6ce",
"canvaskit/canvaskit.js": "66177750aff65a66cb07bb44b8c6422b",
"canvaskit/canvaskit.js.symbols": "48c83a2ce573d9692e8d970e288d75f7",
"canvaskit/canvaskit.wasm": "1f237a213d7370cf95f443d896176460",
"canvaskit/chromium/canvaskit.js": "671c6b4f8fcc199dcc551c7bb125f239",
"canvaskit/chromium/canvaskit.js.symbols": "a012ed99ccba193cf96bb2643003f6fc",
"canvaskit/chromium/canvaskit.wasm": "b1ac05b29c127d86df4bcfbf50dd902a",
"canvaskit/skwasm.js": "694fda5704053957c2594de355805228",
"canvaskit/skwasm.js.symbols": "262f4827a1317abb59d71d6c587a93e2",
"canvaskit/skwasm.wasm": "9f0c0c02b82a910d12ce0543ec130e60",
"canvaskit/skwasm.worker.js": "89990e8c92bcb123999aa81f7e203b1c",
"favicon.png": "900f994c1de63c823b2c020a5d0b665b",
"flutter.js": "f393d3c16b631f36852323de8e583132",
"flutter_bootstrap.js": "03445260bbed5c74155bde661f317cab",
"icons/Icon-192.png": "855c7a411f9193c72b206fca84cfe404",
"icons/Icon-512.png": "1aba06056ad966c28dcc761f30b784a9",
"icons/Icon-maskable-192.png": "855c7a411f9193c72b206fca84cfe404",
"icons/Icon-maskable-512.png": "1aba06056ad966c28dcc761f30b784a9",
"index.html": "99004c0039b9099a7eafd4a57b30a05d",
"/": "99004c0039b9099a7eafd4a57b30a05d",
"loading.gif": "f61742f8d4363b8c03d9b5200794fd66",
"main.dart.js": "474ca888398512684290c55ffd2c9671",
"manifest.json": "5186a129d2819cf2cd75ce0b0e8963c2",
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
