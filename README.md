# نظام إدارة الفعاليات الجامعية

العنوان الظاهر في `pages/about.php`: «نبذة عن المشروع - نظام إدارة الفعاليات». المجلد: `EniEvents`. قاعدة البيانات في `config/database.php`: `eni_events`.

حقائق من `README` السابق أُبقيت حيث طابقت الكود: ثلاثة أنواع حسابات، فلاتر النوع والتاريخ، ألوان الواجهة، خطوات PHP وMySQL المحلية، وحسابات البذرة. أُضيف ما زاده الكود بعد ذلك الدليل (ملف شخصي، نبذة، تصفح عام، ترحيل الاسم، نسخة `EniEvents-static`).

## 1. ما هو المشروع

نظام ويب PHP لإدارة فعاليات جامعية. الصفحات في `pages/` و`index.php` و`browse_events.php`. الواجهات في `api/` ترجع JSON. البيانات في MySQL عبر PDO.

النسخة `EniEvents-static/` صفحات HTML ثابتة (دخول، رئيسية، فعاليات، تفاصيل، حضور، لوحة، إنشاء) بلا اتصال القاعدة الظاهر في أسماء الملفات.

## 2. لماذا يوجد هذا المشروع

`pages/about.php` عنوانها «نبذة عن المشروع». تعليق `database.sql`: «قاعدة بيانات نظام إدارة الفعاليات الجامعية».

الوظائف المكتوبة في الدليل السابق وما زالت في الكود: عرض الفعاليات، تسجيل الطلاب، إنشاء المنظمين، حضور، تقييم بعد الانتهاء، لوحة للأدمن.

## 3. من يستخدمه

`Users.user_type` من نوع `ENUM('student', 'organizer', 'admin')`.

| النوع | ما يسمح به الكود |
| --- | --- |
| زائر غير داخل | `index.php` للدخول أو إنشاء الحساب، و`browse_events.php` لتصفح الفعاليات (تعليق الملف: بدون اشتراط تسجيل الدخول للعرض)، و`pages/about.php` |
| student | `isStudent()`: تسجيل وإلغاء تسجيل، تقييم، `api/my_registrations.php`، `pages/profile.php` |
| organizer | `isOrganizer()`: إنشاء وتعديل وحذف فعالياته، حضور فعالياته، الملف الشخصي |
| admin | `isAdmin()`: `pages/dashboard.php`، وحضور مع المنظم (`attendance.php` يسمح لمنظم أو أدمن) |

`index.php` بعد الدخول: الأدمن إلى `pages/dashboard.php`، والمنظم إلى `pages/home.php`، وغيرهما إلى `pages/profile.php`.

## 4. ماذا يستطيع النظام أن يفعل

- إنشاء حساب `api/register.php` (`password_hash`) ودخول `api/login.php` وخروج `api/logout.php`.
- جلب فعاليات مع بحث ونوع وتاريخ `api/events.php`.
- إنشاء `api/create_event.php` وتحديث `api/update_event.php` وحذف `api/delete_event.php` للمنظم.
- تسجيل طالب `api/register_event.php` وإلغاء `api/unregister_event.php`.
- حضور: `api/attendance.php` بالطريقتين GET وPOST، وصفحة `pages/attendance.php`.
- تقييم `api/review.php` بعد انتهاء `event_date` ومع وجود تسجيل.
- تحديث الاسم `api/update_profile.php` على عمود `full_name`.
- لوحة `pages/dashboard.php`: أعداد Users وEvents وRegistrations وحضور `attended = 1`، وآخر 50 مستخدماً، وقائمة فعاليات.
- تبديل سمة `js/theme.js` وقائمة جوال `js/mobile-menu.js`.

## 5. كيف يعمل النظام

1. `config/session.php` يبدأ `session_start` إن كانت الحالة `PHP_SESSION_NONE`.
2. `config/database.php` ينشئ `$pdo` بترميز `utf8mb4` ووضع `ERRMODE_EXCEPTION` وجلب `FETCH_ASSOC`.
3. `includes/auth.php` يقرأ المستخدم: `isLoggedIn`, `getCurrentUser`, `isAdmin`, `isOrganizer`, `isStudent`, `logout`.
4. الصفحات الداخلية تضم `includes/app_nav.php` (دالة `app_nav_url`).
5. JavaScript (`js/auth.js`, `home.js`, `events.js`, `event_details.js`, `create_event.js`, `attendance.js`, `profile.js`, `landing-auth.js`) يرسل `fetch` إلى `api/`.

## 6. أمثلة واقعية

من `database_seed.sql` (التواريخ نسبية بـ `DATE_ADD(NOW(), ...)`):

| الاسم | النوع | المكان | المقاعد |
| --- | --- | --- | --- |
| ورشة عمل في البرمجة | workshop | قاعة المؤتمرات الرئيسية - مبنى العلوم | 50 |
| محاضرة عن الذكاء الاصطناعي | lecture | المدرج الكبير - كلية الهندسة | 100 |
| مؤتمر التكنولوجيا والابتكار | conference | مركز المؤتمرات - الحرم الجامعي | 200 |

حسابات البذرة (البريد والنوع). كلمة المرور في تعليق الملف ولا تُنسخ هنا:

| البريد | user_type |
| --- | --- |
| admin@university.edu | admin |
| organizer1@university.edu | organizer |
| organizer2@university.edu | organizer |
| student1@university.edu | student |
| student2@university.edu | student |
| student3@university.edu | student |

تعارض مع الدليل السابق: الدليل ذكر `organizer1` و`student1` و`admin` فقط. الملف يحتوي أيضاً `organizer2` و`student2` و`student3`.

## 7. رحلة المستخدم

طالب:

1. `index.php` أو `pages/login.php`: بريد وكلمة مرور (الحد في نموذج `index.php`: `minlength="6"`) ونوع الحساب عند الإنشاء.
2. التوجيه إلى `pages/profile.php`.
3. من «الفعاليات» (`browse_events.php`) أو الرئيسية إلى `pages/event_details.php`.
4. تسجيل عبر `api/register_event.php`.
5. بعد موعد `event_date` تقييم من 1 إلى 5 عبر `api/review.php` إن وُجد صف في `Registrations`.

منظم:

1. الدخول ثم `pages/home.php`.
2. `pages/create_event.php` (يشترط `isOrganizer()`).
3. `pages/attendance.php` لفعاليته فقط (`organizer_id` يطابق الجلسة). الأدمن يتجاوز هذا القيد لأنه ليس فرع المنظم في الشرط `isOrganizer() && organizer_id`.

أدمن:

1. الدخول إلى `pages/dashboard.php`.
2. أعداد المستخدمين والفعاليات والتسجيلات والحضور، وجداول المستخدمين والفعاليات.

## 8. الوحدات والأقسام

| المسار | الدور |
| --- | --- |
| `index.php` | هبوط ودخول؛ يحوّل المسجّل حسب النوع |
| `browse_events.php` | كل الفعاليات للزائر |
| `pages/login.php` | دخول أو إنشاء مع اختيار النوع |
| `pages/home.php` | رئيسية بعد الدخول |
| `pages/about.php` | نبذة |
| `pages/events.php` | قائمة |
| `pages/event_details.php` | تفاصيل |
| `pages/create_event.php` | إنشاء أو تعديل |
| `pages/attendance.php` | حضور |
| `pages/dashboard.php` | لوحة أدمن |
| `pages/profile.php` | ملف شخصي للأنواع الثلاثة |
| `includes/app_nav.php` | شريط يختلف حسب `isOrganizer` و`isStudent` و`isAdmin` |
| `includes/auth.php` | صلاحيات |
| `config/database.php`, `config/session.php` | اتصال وجلسة |
| `css/style.css` | التنسيق |
| `js/*` | سلوك الصفحات |
| `api/*` | JSON |
| `database.sql` | مخطط (يتضمن `full_name`) |
| `database_migration_add_full_name.sql` | إضافة `full_name` لقاعدة قديمة |
| `database_seed.sql` | بذرة |
| `EniEvents-static/` | HTML ثابت مواز |
| `assets/` | دليل سابق: خطوط IBM Plex Sans Arabic و`logo.png` و`assets/images/` |

`app_nav.php` يحمّل الشعار من `assets/logo.png`.

## 9. الشركات والكيانات

لا اسم جامعة في الجداول. نصوص البذرة تستخدم أماكن داخل حرم (قاعات، مدرج، كلية الهندسة). البريد التجريبي على النطاق `university.edu`. Font Awesome من cdnjs مذكور في `pages/about.php`.

## 10. الصلاحيات

| الإجراء | الشرط في الملف |
| --- | --- |
| إنشاء أو تعديل أو حذف فعالية | `isLoggedIn` و`isOrganizer` |
| تسجيل أو إلغاء أو تقييم أو تسجيلاتي | `isStudent` |
| حضور GET/POST | منظم أو أدمن |
| صفحة الحضور لفعالية | أدمن، أو منظم هو `organizer_id` |
| لوحة التحكم في الشريط | `isAdmin()` |
| تحديث الملف | `api/update_profile.php` يشترط POST ومسجلاً (التفاصيل في الملف) |

`isOrganizer()` لا يشمل الأدمن. الأدمن لا يرى رابط «إنشاء فعالية» في `app_nav.php`.

## 11. الأتمتة وسير العمل

- التسجيل في فعالية يكتب `Registrations` (والحضور مسار منفصل في `Attendance`).
- التقييم يرفض إن `strtotime(event_date) > time()` برسالة «يمكن التقييم بعد انتهاء الفعالية فقط».
- التقييم يتطلب صفاً في `Registrations` لنفس `student_id` و`event_id`.
- `Reviews.rating` بين 1 و5 في SQL (`CHECK`) وفي PHP.
- لا طابور ولا بريد ولا مجدول.

حذف مستخدم أو فعالية: `ON DELETE CASCADE` من `database.sql` على المفاتيح الخارجية.

## 12. التكامل بين الوحدات

```
index.php / pages/*.php / browse_events.php
    -> config/session.php
    -> includes/auth.php -> config/database.php -> MySQL eni_events
    -> includes/app_nav.php
    -> js/*.js -> api/*.php -> نفس $pdo
```

`EniEvents-static/` لا يمر على `api/` في بنية الملفات؛ هو لقطات HTML.

## 13. المصطلحات

| المصطلح | المعنى |
| --- | --- |
| user_type | student أو organizer أو admin |
| total_seats / available_seats | مقعدان على `Events` |
| Registrations | تسجيل طالب في فعالية، فريد `(student_id, event_id)` |
| Attendance | مربوط بـ `registration_id` و`attended` و`attendance_time` |
| Reviews | تقييم فريد لكل طالب وفعالية |
| full_name | عمود اختياري أضيف في `database.sql` وفي ملف الترحيل |
| event_type | workshop, lecture, conference, seminar, other في نموذج الإنشاء |
| date_filter | في `events.php`: `upcoming` أو `past` مقارنة بـ `NOW()` |

## 14. الأسئلة الشائعة

**هل أحتاج حساباً لرؤية الفعاليات؟**  
`browse_events.php` يصرح أن العرض بلا تسجيل. التسجيل في الفعالية يتطلب طالباً.

**متى أقيّم؟**  
بعد `event_date` ومع وجود تسجيل.

**ما كلمة مرور البذرة؟**  
مذكورة في تعليق `database_seed.sql` وفي الدليل السابق. لا تُعاد هنا.

**هل الملف الشخصي في الدليل القديم؟**  
الدليل السابق لم يذكر `pages/profile.php` ولا `api/update_profile.php` ولا `api/my_registrations.php`. الملفات موجودة الآن.

## 15. المعمارية (ASCII)

```
[متصفح RTL]
  index.php, browse_events.php, pages/*
  css/style.css, js/*
        | fetch JSON
        v
  api/*.php + includes/auth.php
        |
        v
  PDO -> MySQL eni_events
     Users, Events, Registrations, Attendance, Reviews

  EniEvents-static/*.html   (نسخة ثابتة منفصلة)
```

## 16. التقنيات المستخدمة

| التقنية | أين |
| --- | --- |
| PHP | الصفحات و`api/` |
| PDO MySQL `utf8mb4` | `config/database.php` |
| JavaScript بلا إطار | مجلد `js/` |
| HTML وCSS | `css/style.css` |
| Font Awesome 6.4.0 | cdnjs في `about.php` و`browse_events.php` |
| جلسات PHP | `config/session.php` |
| `password_hash` / تحقق الدخول | `api/register.php` و`api/login.php` |

ألوان موثقة في الدليل السابق وما زال الملف `css/style.css` مرجع التنسيق:

| الدور | القيمة في الدليل السابق |
| --- | --- |
| أساسي | `#2EC4C6` |
| ثانوي | `#1F6ED4` |
| خلفية | `#0F172A` |
| بطاقة | `#1E293B` |
| نص | `#E5E7EB` |

الدليل: الوضع الداكن افتراضي، وزر `#themeToggle` في الشريط نصه «وضع داكن».

إصدار PHP: غير موثق في ملف اعتماديات. لا `composer.json`.

## 17. هيكل المشروع

```
EniEvents/
├── index.php
├── browse_events.php
├── pages/          (home, login, about, events, event_details, create_event, attendance, dashboard, profile)
├── api/            (login, register, logout, events, create/update/delete_event, register/unregister, attendance, review, update_profile, my_registrations)
├── config/database.php
├── config/session.php
├── includes/auth.php
├── includes/app_nav.php
├── css/style.css
├── js/             (auth, home, events, event_details, create_event, attendance, profile, theme, mobile-menu, landing-auth)
├── database.sql
├── database_seed.sql
├── database_migration_add_full_name.sql
├── EniEvents-static/
├── assets/
└── README.md
```

الدليل السابق لم يذكر `profile.php` و`about.php` و`browse_events.php` و`app_nav.php` وملفات JS الجديدة ومجلد النسخة الثابتة. هذه المسارات موجودة في الشجرة الحالية.

## 18. واجهة المستخدم

- `lang="ar"` و`dir="rtl"` في `index.php` و`about.php` و`browse_events.php`.
- شريط: نبذة، الفعاليات، ثم روابط حسب الدور، وزر السمة.
- نماذج الإنشاء: اسم، وصف، `datetime-local`، نوع، مكان، `total_seats`.
- أيقونات Font Awesome عبر `<i>`.
- وسم أيقونة التبويب: راجع الصفحات؛ `app_nav` يستخدم صورة الشعار `assets/logo.png` داخل الصفحة. وجود `<link rel="icon">` في كل الصفحات: غير موثق هنا ملفاً ملفاً.
- النسخة الثابتة تعيد صفحات HTML بأسماء إنجليزية (`home.html`, `events.html`, `dashboard.html`, ...).

## 19. الخادم

`config/database.php`:

| المتغير | القيمة |
| --- | --- |
| host | `localhost` |
| dbname | `eni_events` |
| username | `root` |
| password | فارغة (تعليق الملف: XAMPP) |

فشل الاتصال: `die` مع نص استثناء PDO (قد يظهر تفاصيل تقنية).

لا إطار خادم. التشغيل الموثق في الدليل السابق: `php -S localhost:8000` من مجلد المشروع، أو النسخ إلى `htdocs` وفتح `http://localhost/EniEvents`. مسار الدليل القديم `C:\Users\MoSh\VSCode\Projects\EniEvents` لا يطابق مسار المجلد الحالي `D:\VSCode\Projects\EniEvents`.

## 20. مسار الطلب (real example)

`GET api/events.php?search=ورشة&event_type=workshop&date_filter=upcoming`

1. ترويسة JSON `utf-8`.
2. الاستعلام يبدأ بـ `SELECT e.*, u.email as organizer_email` وعدد التسجيلات من `Events` مع `JOIN Users`.
3. البحث يضيف `name LIKE` أو `description LIKE`.
4. النوع يطابق `event_type`.
5. `upcoming` يضيف `event_date >= NOW()` و`past` يضيف `event_date < NOW()`.
6. الترتيب `event_date ASC`.

`POST api/review.php` بحقول `event_id` و`rating` و`comment`: يرفض غير الطالب، ويرفض تقييماً خارج 1–5، ويرفض فعالية لم تنتهِ، ويطلب تسجيلاً مسبقاً.

## 21. قاعدة البيانات (real tables)

القاعدة `eni_events`، المحرك InnoDB، الترميز `utf8mb4_unicode_ci`.

**Users:** `id`, `email` فريد, `full_name` قابل للإفراغ, `password`, `user_type`, `created_at`. فهارس `idx_email`, `idx_user_type`.

**Events:** `id`, `organizer_id` إلى `Users` مع CASCADE, `name`, `description`, `event_date`, `location`, `total_seats`, `available_seats`, `event_type`, `created_at`, `updated_at`.

**Registrations:** `id`, `student_id`, `event_id`, `registered_at`, فريد `unique_registration`.

**Attendance:** `id`, `registration_id`, `event_id`, `student_id`, `attended` افتراضي FALSE, `attendance_time`.

**Reviews:** `id`, `student_id`, `event_id`, `rating` من 1 إلى 5, `comment`, `created_at`, فريد `unique_review`.

`database_migration_add_full_name.sql` ينفذ `ALTER TABLE Users ADD COLUMN full_name` بعد `email` لمن أنشأ القاعدة قبل وجود العمود. `database.sql` الحالي ينشئ العمود مباشرة. تشغيل الترحيل على قاعدة أُنشئت من `database.sql` الحالي قد يفشل لأن العمود موجود. هذا تعارض تشغيلي بين الملفين.

## 22. واجهات البرمجة

كلها JSON عبر ملفات PHP لا مسار موحّد.

| الملف | الطريقة | من |
| --- | --- | --- |
| `api/login.php` | POST | عام |
| `api/register.php` | POST | عام |
| `api/logout.php` | يستدعي تدمير الجلسة | مسجّل |
| `api/events.php` | GET | بلا شرط دور في رأس الملف |
| `api/create_event.php` | POST | منظم |
| `api/update_event.php` | POST | منظم |
| `api/delete_event.php` | POST | منظم |
| `api/register_event.php` | POST | طالب |
| `api/unregister_event.php` | POST | طالب |
| `api/my_registrations.php` | يتطلب طالباً | طالب |
| `api/attendance.php` | GET وPOST | منظم أو أدمن |
| `api/review.php` | POST وGET | طالب |
| `api/update_profile.php` | POST | مسجّل |

## 23. تسجيل الدخول والصلاحيات

- `api/login.php` يبحث بالبريد و`user_type` (كما في الدليل السابق: الاستعلام يصفّي النوع أيضاً).
- الجلسة تحفظ `user_id`. `getCurrentUser` يعيد الصف من `Users`.
- الخروج: `logout()` في `auth.php` ينفذ `session_destroy` ثم يحوّل إلى `../index.php`.
- إنشاء الحساب من `index.php`: القائمة `student` أو `organizer` فقط. النص في الصفحة: حساب المسؤول لا يُنشأ من هنا.
- `api/register.php` يقبل `student` أو `organizer` فقط. أي قيمة أخرى تُحفظ `student`. كلمة المرور أقصر من 6 أحرف تُرفض. البريد المكرر يُرفض.

ملفات تعريف الجلسة `HttpOnly` و`Secure` و`SameSite`: غير مذكورة في `config/session.php` (يستدعي `session_start` فقط).

## 24. الحماية

- PDO `prepare` في `auth.php` و`events.php` و`review.php` (حسب الملفات المقروءة).
- `password_hash` عند التسجيل.
- `htmlspecialchars` في حقول تُطبع في `create_event.php` و`app_nav.php`.
- فحص الدور قبل الكتابة في واجهات الفعاليات والحضور والتقييم.
- رسائل رفض JSON مثل «غير مصرح».
- فشل اتصال القاعدة يطبع رسالة الاستثناء.
- لا رموز CSRF في `config/session.php`.
- لا حد لمحاولات الدخول في الملفات الحالية.
- لا `.htaccess` ظاهر في قائمة الملفات.

## 25. الإعدادات

لا `.env`. الإعداد في `config/database.php` و`config/session.php`.

متغيرات السمة في CSS موثقة في الدليل السابق (القسم 16).

## 26. التكاملات الخارجية

Font Awesome 6.4.0 من `cdnjs.cloudflare.com` في صفحات مثل `about.php`. لا بوابة دفع ولا بريد ولا SMS في الملفات الحالية.

## 27. المهام المجدولة

غير موجود في الملفات الحالية. حالة «قادمة / منتهية» تُحسب وقت الطلب بمقارنة `event_date` مع `NOW()` أو `time()`.

## 28. تخزين الملفات

لا جدول مرفقات ولا مجلد `uploads` في `database.sql`. الشعار مسار ثابت `assets/logo.png`. الخطوط حسب الدليل السابق تحت `assets/fonts/`.

## 29. السجلات والمتابعة

لا مجلد `logs` ولا جدول تدقيق. الأخطاء في مسار الاتصال تُطبع عبر `die`. أخطاء الواجهة: الدليل السابق يذكر وحدة المتصفح (F12).

`pages/dashboard.php` يعرض إحصاءات لحظية لا سجلاً تاريخياً.

## 30. التثبيت

1. PHP مع PDO وPDO_MySQL (الدليل السابق).
2. MySQL.
3. تنفيذ `database.sql` لإنشاء `eni_events`.
4. اختيارياً `database_seed.sql`. لا تشغّل `database_migration_add_full_name.sql` إن أُنشئت القاعدة من `database.sql` الحالي (العمود موجود).
5. طابق `config/database.php`.
6. من مجلد المشروع:

```
php -S localhost:8000
```

أو انسخ المجلد إلى جذر Apache وافتح المسار المحلي.

## 31. دليل التطوير

- أبقِ فحص `isStudent` و`isOrganizer` و`isAdmin` في أي واجهة جديدة.
- `app_nav_url` يعتمد على `$APP_NAV_FROM_ROOT`: من الجذر `true` (`browse_events.php`) ومن `pages/` القيمة false.
- أنواع الفعالية في النموذج نصية وليست ENUM في SQL (`event_type VARCHAR(100)`).
- النسخة `EniEvents-static` لا تتحدث تلقائياً مع PHP. أي تغيير واجهة يحتاج قراراً منفصلاً إن أردت مزامنتها.
- لا ترفع كلمة مرور البذرة إلى بيئة عامة.

## 32. النشر

غير موثق. الإعدادات المحلية `localhost` و`root` وكلمة فارغة. لا Docker ولا ملف استضافة.

## 33. النسخ الاحتياطي والاستعادة

غير موثق كإجراء. الاستعادة من الملفات: إعادة `database.sql` ثم `database_seed.sql` عند الحاجة. هذا يحذف الحاجة للترحيل إن أُعيد المخطط الكامل.

## 34. تشخيص المشكلات

| العرض | الفحص |
| --- | --- |
| خطأ في الاتصال | MySQL وبيانات `config/database.php` واسم `eni_events` |
| الجلسة لا تثبت | `session_start` في `config/session.php` وصلاحيات مجلد جلسات PHP |
| الصفحة بلا بيانات | تبويب الشبكة لطلبات `api/` وأن الرد JSON |
| التقييم مرفوض | الموعد لم يمر أو لا تسجيل |
| عمود full_name مكرر | تشغيل الترحيل فوق `database.sql` الحالي |
| دخول البذرة يفشل | نوع الحساب المختار يجب أن يطابق `user_type` لأن `login.php` يصفّي النوع (حسب شرح الدليل السابق للاستعلام) |

## 35. الاعتماديات

لا Composer ولا npm في الملفات الحالية. PHP وMySQL ومتصفح. Font Awesome عبر CDN. خط IBM Plex Sans Arabic مذكور في الدليل السابق كملفات محلية تحت `assets/fonts/`.

## 36. القيود المعروفة

- حساب `admin` لا يُنشأ من `api/register.php` ولا من قائمة `index.php`. إنشاؤه يتم عبر SQL (مثل `database_seed.sql`) أو إدخال مباشر في `Users`.
- خصائص أمان ملف تعريف الجلسة غير مضبوطة في `session.php`.
- خطأ PDO يُطبع للمتصفح.
- لا CSRF ولا حد محاولات.
- `available_seats` عمود في الجدول؛ مدى تحديثه عند كل تسجيل يعتمد على `api/register_event.php` (المنطق التفصيلي لكل سطر غير معاد هنا).
- نسختان: ديناميكية وثابتة.
- مسار التشغيل في الدليل القديم يشير إلى مجلد مستخدم `MoSh` لا إلى المسار الحالي.
- لا أيقونة تبويب موثقة كشرط في كل الصفحات ضمن هذا الملخص.

## 37. حالة النظام الحالية

تطبيق PHP محلي كامل المسارات المذكورة، مع بذرة اختيارية ونسخة HTML ثابتة. لا اختبارات آلية في الشجرة. لا رقم إصدار.

## 38. قرارات المعمارية

استنتاج من الكود: الصفحات PHP ترسم الهيكل والشريط، والبيانات التفاعلية JSON حتى تبقى الفلاتر في `js/events.js` دون إعادة بناء كاملة لكل نقرة. الصلاحية تُعاد قراءتها من `Users.user_type` عبر `getCurrentUser()` في كل نداء `isAdmin` وليس من نسخة مخزنة فقط في الجلسة عدا `user_id`.

استنتاج من الكود: `EniEvents-static` أرشيف واجهة للعرض بلا خادم.

## 39. سجل التغييرات

غير موثق كملف إصدارات. فرق بين الدليل السابق والشجرة الحالية:

- أضيف في الكود: `full_name` وملف الترحيل، `profile.php`, `update_profile.php`, `my_registrations.php`, `about.php`, `browse_events.php`, `app_nav.php`, `theme.js`, `mobile-menu.js`, `landing-auth.js`, `profile.js`, مجلد `EniEvents-static`.
- الدليل السابق وثّق الألوان والحسابات الثلاثة الأولى وخطوات `php -S localhost:8000`.

## System Overview

University event system on PHP, PDO, and MySQL database `eni_events`. Roles are student, organizer, and admin. Students register and review after the event date. Organizers manage their events and attendance. Admins get `pages/dashboard.php`. A static HTML copy sits in `EniEvents-static/`.

## Quick Reference

| البند | القيمة |
| --- | --- |
| القاعدة | `eni_events` |
| الجداول | Users, Events, Registrations, Attendance, Reviews |
| الاتصال | `config/database.php` |
| الجلسة | `config/session.php` |
| الصلاحيات | `includes/auth.php` |
| البذرة | `database_seed.sql` |
| الترحيل | `database_migration_add_full_name.sql` |

## Quick Start

```
mysql -u root -p < database.sql
mysql -u root -p eni_events < database_seed.sql
php -S localhost:8000
```

ثم افتح `http://localhost:8000`. اقرأ كلمة مرور البذرة من تعليق `database_seed.sql`.

## For Non-Technical Users

إن لم يكن لديك حساب فصفحة «الفعاليات» تعرض القائمة. لإنشاء حساب اختر نوعه في النموذج. الطالب يسجل في الفعالية ويقيّمها بعد موعدها. المنظم ينشئ الفعالية ويأخذ الحضور. المسؤول يرى لوحة الأعداد والمستخدمين.

## For Developers

ابدأ من `includes/auth.php` و`includes/app_nav.php` ثم `api/events.php`. لا تشغّل ترحيل `full_name` على مخطط يحتوي العمود. لا تطبع استثناء PDO في النشر. `api/register.php` يمنع `user_type=admin`.
