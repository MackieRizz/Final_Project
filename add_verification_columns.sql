ALTER TABLE students_registration
ADD COLUMN verification_code VARCHAR(6) NULL,
ADD COLUMN verification_expiry DATETIME NULL; 