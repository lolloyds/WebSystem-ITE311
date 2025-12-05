-- SQL script to add status column to users table
-- Run this script if you prefer to add the column manually instead of using migrations

ALTER TABLE `users` 
ADD COLUMN `status` ENUM('active', 'inactive') DEFAULT 'active' AFTER `role`;

-- Update all existing users to 'active' status (if needed)
UPDATE `users` SET `status` = 'active' WHERE `status` IS NULL;

