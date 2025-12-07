# Multi-Tenancy (المنظمات المتعددة)

هذا المستند يشرح كيفية استخدام ميزة المنظمات المتعددة في النظام.

## المفهوم

النظام يدعم الآن تعدد المنظمات في قاعدة بيانات واحدة، بحيث:
- كل مستخدم يمكن أن ينتمي لمنظمة واحدة أو أكثر
- كل مستخدم له دور في كل منظمة (مدير، مشرف، عضو)
- البيانات معزولة بحيث كل مستخدم يرى فقط بيانات منظمته الحالية
- يمكن للمستخدم التبديل بين المنظمات المسموح له بالوصول إليها

## التثبيت

### 1. تشغيل الهجرات

```bash
php artisan migrate
```

هذا سينشئ:
- جدول `organizations` - لتخزين معلومات المنظمات
- جدول `organization_user` - العلاقة بين المستخدمين والمنظمات
- عمود `organization_id` في جميع الجداول المطلوبة

### 2. تشغيل البذور (Seeders)

```bash
php artisan db:seed --class=OrganizationSeeder
```

هذا سيقوم بـ:
- إنشاء منظمة افتراضية
- تعيين جميع المستخدمين الحاليين للمنظمة الافتراضية
- تحديث جميع البيانات الموجودة بـ `organization_id`

### 3. تحديث الصلاحيات

```bash
php artisan db:seed --class=PermissionsSeeder
```

## كيفية الاستخدام

### إدارة المنظمات

1. يجب أن يكون لديك صلاحية `manage_organizations`
2. اذهب إلى "إدارة المنظمات" من القائمة الجانبية
3. يمكنك:
   - إضافة منظمة جديدة
   - تعديل معلومات المنظمة
   - إضافة/إزالة مستخدمين
   - تغيير أدوار المستخدمين

### التبديل بين المنظمات

إذا كنت تنتمي لأكثر من منظمة:
1. سترى قائمة منسدلة في الشريط الجانبي تحت اسمك
2. اضغط على اسم المنظمة الحالية
3. اختر المنظمة التي تريد التبديل إليها
4. سيتم تحديث الصفحة تلقائياً

### أدوار المستخدمين في المنظمة

| الدور | الوصف |
|-------|-------|
| `admin` (مدير) | صلاحيات كاملة في المنظمة |
| `manager` (مشرف) | صلاحيات إدارية محدودة |
| `member` (عضو) | صلاحيات عادية |

## الهيكل التقني

### الجداول الجديدة

#### organizations
```
- id
- name (اسم المنظمة)
- slug (المعرف الفريد)
- logo (الشعار)
- description (الوصف)
- is_active (نشط/غير نشط)
- timestamps
```

#### organization_user
```
- id
- organization_id
- user_id
- role (admin, manager, member)
- is_default (المنظمة الافتراضية للمستخدم)
- timestamps
```

### الـ Trait

جميع النماذج (Models) التي تحتاج عزل البيانات تستخدم الـ Trait:

```php
use App\Traits\BelongsToOrganization;

class Task extends Model
{
    use BelongsToOrganization;
    // ...
}
```

### الـ Middleware

هناك middleware مسجل لإعداد المنظمة الحالية:

- `SetOrganization` - يعمل على كل الطلبات لتعيين المنظمة الحالية
- `EnsureOrganizationAccess` - للتحقق من صلاحية الوصول للمنظمة

### الاستعلامات

البيانات يتم فلترتها تلقائياً حسب المنظمة الحالية:

```php
// هذا سيجلب فقط المهام التابعة للمنظمة الحالية
$tasks = Task::all();

// للحصول على بيانات بدون فلتر المنظمة
$allTasks = Task::withoutOrganization()->get();

// للحصول على بيانات منظمة محددة
$orgTasks = Task::forOrganization($organizationId)->get();
```

### إضافة بيانات جديدة

عند إضافة سجل جديد، يتم تعيين `organization_id` تلقائياً:

```php
// organization_id سيتم تعيينه تلقائياً
$task = Task::create([
    'name' => 'مهمة جديدة',
    // ...
]);
```

## التخصيص

### إضافة جدول جديد للمنظمات

1. أضف عمود `organization_id` في الهجرة:
```php
$table->foreignId('organization_id')->nullable()->after('id');
$table->index('organization_id');
```

2. أضف الـ Trait للنموذج:
```php
use App\Traits\BelongsToOrganization;

class NewModel extends Model
{
    use BelongsToOrganization;
}
```

### تعطيل العزل مؤقتاً

في بعض الحالات قد تحتاج للوصول لبيانات بدون فلتر المنظمة:

```php
// طريقة 1: استخدام withoutOrganization
$data = Model::withoutOrganization()->where(...)->get();

// طريقة 2: حذف الـ Global Scope يدوياً
use App\Scopes\OrganizationScope;
$data = Model::withoutGlobalScope(OrganizationScope::class)->get();
```

## الملاحظات المهمة

1. **البيانات القديمة**: تأكد من تشغيل OrganizationSeeder لتحديث البيانات القديمة
2. **المستخدم الجديد**: المستخدم الجديد لن يرى أي بيانات حتى يتم تعيينه لمنظمة
3. **الصلاحيات**: صلاحيات المستخدم مستقلة عن المنظمة، لكن دوره في المنظمة قد يؤثر على بعض الوظائف

## استكشاف الأخطاء

### المستخدم لا يرى بيانات

1. تأكد من أن المستخدم معين لمنظمة
2. تأكد من أن المنظمة نشطة (`is_active = true`)
3. تأكد من أن البيانات لها `organization_id` صحيح

### خطأ "لم يتم تعيينك لأي منظمة"

1. اذهب لإدارة المنظمات
2. أضف المستخدم للمنظمة المطلوبة

### البيانات تظهر فارغة بعد التحديث

تأكد من تشغيل:
```bash
php artisan db:seed --class=OrganizationSeeder
```
