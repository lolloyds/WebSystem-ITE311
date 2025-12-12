-- SQL script to update enrollments table status column
-- Run this script to fix the status column enum values

-- First, update all existing 'active' statuses to 'approved'
UPDATE `enrollments` SET `status` = 'approved' WHERE `status` = 'active';

-- Modify the status column to use correct enum values with 'pending' as default
ALTER TABLE `enrollments`
MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending';

-- Update any remaining old values that don't match the new enum
UPDATE `enrollments` SET `status` = 'pending' WHERE `status` NOT IN ('pending', 'approved', 'rejected');
