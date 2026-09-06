# Facefood — Android Build & Google Play Store Guide

Complete guide to build an **APK** (install / test) and an **AAB** (required for Play Store), then submit **Facefood** (`facefood_app`).

| Item | Value |
|------|--------|
| App folder | `facefood_app/` |
| App name (launcher) | Facefood |
| Application ID | `com.facefood.facefood_app` |
| Package / namespace | `com.facefood.facefood_app` |
| Version | `1.0.0+1` in `pubspec.yaml` → `versionName` **1.0.0**, `versionCode` **1** |
| Flutter | 3.44+ / Dart 3.12+ |
| Permission | `INTERNET` only |

> **Play Store rule:** Google Play accepts **Android App Bundle (`.aab`)**, not a raw APK, for new apps. Use APK for local install / testing; use AAB for store upload.

---

## Table of contents

1. [Prerequisites](#1-prerequisites)
2. [Pre-release checklist (do this first)](#2-pre-release-checklist-do-this-first)
3. [Create a release keystore](#3-create-a-release-keystore)
4. [Configure release signing](#4-configure-release-signing)
5. [Build APK](#5-build-apk)
6. [Build AAB for Play Store](#6-build-aab-for-play-store)
7. [Test the release build](#7-test-the-release-build)
8. [Google Play Console setup](#8-google-play-console-setup)
9. [Upload & submit for review](#9-upload--submit-for-review)
10. [Update an already published app](#10-update-an-already-published-app)
11. [Troubleshooting](#11-troubleshooting)

---

## 1. Prerequisites

Install and verify:

```bash
flutter doctor -v
```

You need:

- [Flutter SDK](https://docs.flutter.dev/get-started/install) (stable)
- Android SDK (via Android Studio is easiest)
- JDK 17 (this project targets Java 17)
- A Google account + [Google Play Console](https://play.google.com/console) developer account (**one-time fee**)

From the app folder:

```bash
cd facefood_app
flutter pub get
flutter devices   # optional: see connected phones / emulators
```

---

## 2. Pre-release checklist (do this first)

Before any Play Store build, fix these Facefood-specific items.

### 2.1 Point the app at your live API (HTTPS)

Edit `facefood_app/lib/config/app_config.dart`:

```dart
static const String apiBaseUrl = 'https://YOUR-DOMAIN.com/api';
```

Or pass it at build time (recommended so you don’t hardcode):

```bash
flutter build appbundle --release \
  --dart-define=API_BASE_URL=https://YOUR-DOMAIN.com/api
```

| Environment | Example `API_BASE_URL` |
|-------------|------------------------|
| Android emulator (dev) | `http://10.0.2.2:8000/api` |
| Physical phone on LAN | `http://192.168.x.x:8000/api` |
| **Production / Play Store** | `https://api.yourdomain.com/api` |

Deploy the Laravel API (`backend/`) on a public HTTPS host. The store build must reach a real server, not `10.0.2.2` or `127.0.0.1`.

### 2.2 Turn off cleartext HTTP for production

`AndroidManifest.xml` currently has:

```xml
android:usesCleartextTraffic="true"
```

That allows plain HTTP (fine for local debug). For Play Store / production:

1. Host the API on **HTTPS**.
2. Set `android:usesCleartextTraffic="false"` (or remove the attribute).
3. Rebuild.

File: `facefood_app/android/app/src/main/AndroidManifest.xml`

### 2.3 Use a real release signing key

`android/app/build.gradle.kts` currently signs **release** with the **debug** key:

```kotlin
signingConfig = signingConfigs.getByName("debug")
```

Play Store rejects (or later blocks updates for) apps signed only with debug keys long-term. Follow [§3](#3-create-a-release-keystore) and [§4](#4-configure-release-signing).

### 2.4 Bump version for each store upload

In `facefood_app/pubspec.yaml`:

```yaml
version: 1.0.0+1
#          │     │
#          │     └── versionCode (integer) — must increase every Play upload
#          └── versionName (user-visible, e.g. 1.0.1)
```

Examples:

- First release: `1.0.0+1`
- Bugfix: `1.0.1+2`
- Feature: `1.1.0+3`

### 2.5 App icon & name

- Launcher name: `android:label="Facefood"` in `AndroidManifest.xml`
- Icons: `android/app/src/main/res/mipmap-*`
- Optionally regenerate icons with [flutter_launcher_icons](https://pub.dev/packages/flutter_launcher_icons)

### 2.6 Privacy policy & store assets

Play Console will ask for:

- Privacy policy URL (required if you collect accounts, addresses, orders — Facefood does)
- Short & full description
- Screenshots (phone; tablet if you support it)
- Feature graphic (1024×500)
- App icon (512×512 high-res)
- Content rating questionnaire
- Data safety form
- Target audience / ads declaration

---

## 3. Create a release keystore

**Do this once.** Back up the keystore and passwords. If you lose them, you cannot update the same Play listing with a new signing key (unless you use Play App Signing recovery options).

```bash
cd facefood_app/android/app

keytool -genkey -v -keystore facefood-upload-keystore.jks \
  -keyalg RSA -keysize 2048 -validity 10000 \
  -alias facefood \
  -storetype JKS
```

You will be asked for:

- Keystore password
- Key password
- Name / org details

Suggested layout (keep secrets out of git):

```
facefood_app/android/
├── key.properties          # NOT committed
└── app/
    └── facefood-upload-keystore.jks   # NOT committed (or store elsewhere safely)
```

Add to `facefood_app/.gitignore` (if not already):

```
**/android/key.properties
**/android/app/*.jks
**/android/app/*.keystore
```

Create `facefood_app/android/key.properties`:

```properties
storePassword=YOUR_STORE_PASSWORD
keyPassword=YOUR_KEY_PASSWORD
keyAlias=facefood
storeFile=facefood-upload-keystore.jks
```

`storeFile` is relative to `android/app/` when configured as in §4.

---

## 4. Configure release signing

Replace the contents of `facefood_app/android/app/build.gradle.kts` with a signed release setup like this:

```kotlin
plugins {
    id("com.android.application")
    id("dev.flutter.flutter-gradle-plugin")
}

import java.util.Properties
import java.io.FileInputStream

val keystoreProperties = Properties()
val keystorePropertiesFile = rootProject.file("key.properties")
if (keystorePropertiesFile.exists()) {
    keystoreProperties.load(FileInputStream(keystorePropertiesFile))
}

android {
    namespace = "com.facefood.facefood_app"
    compileSdk = flutter.compileSdkVersion
    ndkVersion = flutter.ndkVersion

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }

    defaultConfig {
        applicationId = "com.facefood.facefood_app"
        minSdk = flutter.minSdkVersion
        targetSdk = flutter.targetSdkVersion
        versionCode = flutter.versionCode
        versionName = flutter.versionName
    }

    signingConfigs {
        create("release") {
            keyAlias = keystoreProperties["keyAlias"] as String
            keyPassword = keystoreProperties["keyPassword"] as String
            storeFile = keystoreProperties["storeFile"]?.let { file(it) }
            storePassword = keystoreProperties["storePassword"] as String
        }
    }

    buildTypes {
        release {
            signingConfig = signingConfigs.getByName("release")
        }
    }
}

kotlin {
    compilerOptions {
        jvmTarget = org.jetbrains.kotlin.gradle.dsl.JvmTarget.JVM_17
    }
}

flutter {
    source = "../.."
}
```

Confirm `key.properties` exists under `facefood_app/android/` before building release.

---

## 5. Build APK

APK = installable file for phones / emulators. Good for QA; **not** the primary Play Store upload format.

### Debug APK (development)

```bash
cd facefood_app
flutter build apk --debug
```

Output:

```
build/app/outputs/flutter-apk/app-debug.apk
```

### Release APK (signed, production-like)

```bash
cd facefood_app
flutter build apk --release \
  --dart-define=API_BASE_URL=https://YOUR-DOMAIN.com/api
```

Output:

```
build/app/outputs/flutter-apk/app-release.apk
```

### Split APKs per ABI (smaller downloads)

```bash
flutter build apk --release --split-per-abi \
  --dart-define=API_BASE_URL=https://YOUR-DOMAIN.com/api
```

Outputs (examples):

```
build/app/outputs/flutter-apk/app-armeabi-v7a-release.apk
build/app/outputs/flutter-apk/app-arm64-v8a-release.apk
build/app/outputs/flutter-apk/app-x86_64-release.apk
```

### Install APK on a device

```bash
adb install -r build/app/outputs/flutter-apk/app-release.apk
```

Or copy the APK to the phone and open it (enable “Install unknown apps” if prompted).

---

## 6. Build AAB for Play Store

This is what you upload to Google Play.

```bash
cd facefood_app
flutter clean
flutter pub get
flutter build appbundle --release \
  --dart-define=API_BASE_URL=https://YOUR-DOMAIN.com/api
```

Output:

```
build/app/outputs/bundle/release/app-release.aab
```

Upload **`app-release.aab`** in Play Console.

Optional: verify the bundle locally with [bundletool](https://developer.android.com/tools/bundletool) or Android Studio → Build → Analyze APK / App Bundle.

---

## 7. Test the release build

```bash
# On a connected device / emulator
flutter install --release \
  --dart-define=API_BASE_URL=https://YOUR-DOMAIN.com/api

# Or run release mode
flutter run --release \
  --dart-define=API_BASE_URL=https://YOUR-DOMAIN.com/api
```

Checklist:

- [ ] Login / register works against production API
- [ ] Home, menu, deals, cart load
- [ ] Checkout / order flow works
- [ ] Images load (HTTPS image URLs)
- [ ] No crash on cold start
- [ ] App name shows as **Facefood**

---

## 8. Google Play Console setup

1. Go to [Google Play Console](https://play.google.com/console) and pay the developer registration fee if you haven’t.
2. **Create app** → name **Facefood**, default language, free/paid, declarations.
3. Complete **Dashboard** setup tasks:
   - App access
   - Ads declaration
   - Content rating
   - Target audience
   - News app / COVID / Data safety, etc.
   - Privacy policy URL
4. **Main store listing**
   - Title, short description, full description
   - Screenshots, icon, feature graphic
   - Contact email / website
5. **App integrity / App signing**
   - Prefer **Play App Signing** (Google holds the app signing key; you upload with the upload keystore from §3).
6. **Countries / pricing**
7. **App content** → Data safety (accounts, location/addresses if collected, etc.)

### Facefood notes for Data safety / privacy

Typical disclosures for this app:

- Account email / name (auth)
- Delivery addresses
- Order history
- Network activity to your API

Host a privacy policy page that explains what you store and why.

---

## 9. Upload & submit for review

1. Play Console → your app → **Production** (or **Internal testing** / **Closed testing** first — recommended).
2. **Create new release**
3. Upload `facefood_app/build/app/outputs/bundle/release/app-release.aab`
4. Release name (e.g. `1.0.0 (1)`) and release notes
5. **Review release** → fix any errors (target SDK, signing, missing store listing fields)
6. **Start rollout to Production** (or send to testers first)

Recommended path for a first app:

```
Internal testing → Closed testing → Open testing → Production
```

Internal testing often reviews faster and catches crashes before public users.

---

## 10. Update an already published app

1. Change version in `pubspec.yaml` — **increase `+` build number** every time:

   ```yaml
   version: 1.0.1+2
   ```

2. Rebuild AAB:

   ```bash
   cd facefood_app
   flutter build appbundle --release \
     --dart-define=API_BASE_URL=https://YOUR-DOMAIN.com/api
   ```

3. Play Console → Production (or testing track) → **Create new release** → upload new `.aab` → rollout.

Use the **same upload keystore** as the first release.

---

## 11. Troubleshooting

| Problem | Fix |
|---------|-----|
| App can’t reach API on phone | Set production HTTPS URL; don’t use `10.0.2.2` in store builds |
| Cleartext / HTTP blocked | Use HTTPS and set `usesCleartextTraffic="false"` |
| `key.properties` not found | File must be at `facefood_app/android/key.properties` |
| Wrong signing / “App not installed” | Uninstall debug build first; debug and release signatures differ |
| Play rejects target SDK | Upgrade Flutter / Android Gradle so `targetSdk` meets Play’s minimum |
| versionCode already used | Bump the `+N` number in `pubspec.yaml` |
| AAB too large | Use `flutter build appbundle`; avoid shipping unused assets |
| Release still debug-signed | Confirm `signingConfigs.release` is wired in `build.gradle.kts` |

Check build logs:

```bash
flutter build appbundle --release -v
```

---

## Quick command cheat sheet

```bash
cd facefood_app

# Dev debug APK
flutter build apk --debug

# Production APK (sideload / QA)
flutter build apk --release \
  --dart-define=API_BASE_URL=https://YOUR-DOMAIN.com/api

# Play Store upload (AAB)
flutter build appbundle --release \
  --dart-define=API_BASE_URL=https://YOUR-DOMAIN.com/api

# Outputs
# APK → build/app/outputs/flutter-apk/app-release.apk
# AAB → build/app/outputs/bundle/release/app-release.aab
```

---

## Related project files

| File | Purpose |
|------|---------|
| `facefood_app/pubspec.yaml` | App version (`1.0.0+1`) |
| `facefood_app/lib/config/app_config.dart` | API base URL |
| `facefood_app/android/app/build.gradle.kts` | `applicationId`, signing |
| `facefood_app/android/app/src/main/AndroidManifest.xml` | Name, permissions, cleartext |
| `backend/` | Laravel API — must be live HTTPS for store builds |
| Root `README.md` | API + Flutter run instructions |

Official references:

- [Flutter — Build and release an Android app](https://docs.flutter.dev/deployment/android)
- [Play Console Help](https://support.google.com/googleplay/android-developer)
