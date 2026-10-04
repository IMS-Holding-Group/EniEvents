-- تشغيل مرة واحدة على قواعد بيانات قائمة لإضافة عمود الاسم
USE eni_events;
ALTER TABLE Users ADD COLUMN full_name VARCHAR(255) NULL AFTER email;
