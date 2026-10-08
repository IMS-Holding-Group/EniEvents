-- بيانات افتراضية لنظام إدارة الفعاليات

USE eni_events;

-- إضافة مستخدمين افتراضيين
INSERT INTO Users (email, password, user_type) VALUES
('admin@university.edu', '', 'admin'),
('organizer1@university.edu', '', 'organizer'),
('organizer2@university.edu', '', 'organizer'),
('student1@university.edu', '', 'student'),
('student2@university.edu', '', 'student'),
('student3@university.edu', '', 'student');

-- كلمة المرور الافتراضية لجميع المستخدمين: password

-- إضافة فعاليات افتراضية
INSERT INTO Events (organizer_id, name, description, event_date, location, total_seats, available_seats, event_type) VALUES
(2, 'ورشة عمل في البرمجة', 'ورشة عمل شاملة عن أساسيات البرمجة وتطوير الويب باستخدام HTML, CSS, JavaScript', DATE_ADD(NOW(), INTERVAL 7 DAY), 'قاعة المؤتمرات الرئيسية - مبنى العلوم', 50, 50, 'workshop'),
(2, 'محاضرة عن الذكاء الاصطناعي', 'محاضرة تفاعلية عن أحدث التطورات في مجال الذكاء الاصطناعي وتطبيقاته العملية', DATE_ADD(NOW(), INTERVAL 10 DAY), 'المدرج الكبير - كلية الهندسة', 100, 100, 'lecture'),
(3, 'مؤتمر التكنولوجيا والابتكار', 'مؤتمر سنوي يجمع الخبراء والمهتمين في مجال التكنولوجيا والابتكار لعرض أحدث الأبحاث والتطبيقات', DATE_ADD(NOW(), INTERVAL 15 DAY), 'مركز المؤتمرات - الحرم الجامعي', 200, 200, 'conference'),
(2, 'ندوة عن ريادة الأعمال', 'ندوة حوارية مع رواد أعمال ناجحين لمناقشة التحديات والفرص في عالم ريادة الأعمال', DATE_ADD(NOW(), INTERVAL 5 DAY), 'قاعة الندوات - مبنى الإدارة', 80, 80, 'seminar'),
(3, 'ورشة تصميم الجرافيك', 'ورشة عملية لتعلم أساسيات التصميم الجرافيكي باستخدام أدوات حديثة', DATE_ADD(NOW(), INTERVAL 12 DAY), 'معمل الحاسوب - كلية الفنون', 30, 30, 'workshop'),
(2, 'محاضرة عن الأمن السيبراني', 'محاضرة توعوية عن أهمية الأمن السيبراني وكيفية حماية البيانات الشخصية والمؤسسية', DATE_ADD(NOW(), INTERVAL 8 DAY), 'المدرج الصغير - كلية الحاسوب', 60, 60, 'lecture'),
(3, 'ندوة عن الاستدامة البيئية', 'ندوة لمناقشة قضايا البيئة والاستدامة ودور الجامعات في الحفاظ على البيئة', DATE_ADD(NOW(), INTERVAL 20 DAY), 'قاعة المحاضرات - كلية العلوم', 90, 90, 'seminar'),
(2, 'مؤتمر البحث العلمي', 'مؤتمر لعرض أحدث الأبحاث العلمية للطلاب وأعضاء هيئة التدريس', DATE_ADD(NOW(), INTERVAL 25 DAY), 'مركز الأبحاث - الحرم الجامعي', 150, 150, 'conference'),
(3, 'ورشة تطوير تطبيقات الموبايل', 'ورشة عملية لتعلم تطوير تطبيقات الموبايل باستخدام Flutter و React Native', DATE_ADD(NOW(), INTERVAL 14 DAY), 'معمل البرمجة - مبنى الحاسوب', 40, 40, 'workshop'),
(2, 'محاضرة عن إدارة المشاريع', 'محاضرة عن أساسيات إدارة المشاريع وأدواتها الحديثة', DATE_ADD(NOW(), INTERVAL 6 DAY), 'قاعة الاجتماعات - مبنى الإدارة', 70, 70, 'lecture');

-- إضافة بعض التسجيلات الافتراضية
INSERT INTO Registrations (student_id, event_id) VALUES
(4, 1),
(4, 2),
(5, 1),
(5, 3),
(6, 2),
(6, 4);

-- تحديث المقاعد المتاحة
UPDATE Events SET available_seats = total_seats - (SELECT COUNT(*) FROM Registrations WHERE event_id = Events.id);

-- إضافة بعض التقييمات للفعاليات المنتهية (فعاليات قديمة)
INSERT INTO Events (organizer_id, name, description, event_date, location, total_seats, available_seats, event_type) VALUES
(2, 'ورشة سابقة - تطوير الويب', 'ورشة سابقة عن تطوير الويب', DATE_SUB(NOW(), INTERVAL 10 DAY), 'قاعة المؤتمرات', 50, 0, 'workshop'),
(3, 'محاضرة سابقة - البيانات الضخمة', 'محاضرة سابقة عن البيانات الضخمة', DATE_SUB(NOW(), INTERVAL 5 DAY), 'المدرج الكبير', 100, 0, 'lecture');

-- تسجيلات للفعاليات السابقة
INSERT INTO Registrations (student_id, event_id) VALUES
(4, 11),
(5, 11),
(6, 12);

-- تحديث المقاعد
UPDATE Events SET available_seats = 0 WHERE id IN (11, 12);

-- تقييمات للفعاليات السابقة
INSERT INTO Reviews (student_id, event_id, rating, comment) VALUES
(4, 11, 5, 'ورشة رائعة ومفيدة جداً، تعلمت الكثير من الأشياء الجديدة'),
(5, 11, 4, 'جيدة جداً ولكن تحتاج وقت أطول'),
(6, 12, 5, 'محاضرة ممتازة، المحاضر كان واضحاً ومهماً');
