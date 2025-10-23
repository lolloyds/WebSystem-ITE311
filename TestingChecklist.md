# Manual Testing Checklist for Materials Upload Feature

This checklist covers the file upload feature for teachers and admins, including upload, view, download, delete, and permissions. Test each case manually and record pass/fail.

## Prerequisites
- Database migration run: `php spark migrate`
- Users created: At least one admin, one teacher, one student
- Courses created: At least one course per teacher
- Student enrolled in a course
- Files ready: Valid (PDF, DOCX, PPT, JPG <10MB), invalid (EXE, >10MB)

## 1. Upload Functionality

### Test Case 1.1: Teacher Upload Valid File
- **Steps:**
  1. Login as teacher
  2. Navigate to teacher dashboard
  3. Click "Upload Material" for a course
  4. Select a valid file (e.g., sample.pdf)
  5. Click Upload
- **Expected Result:** Success message displayed, material appears in the list with file name, upload date, Download and Delete buttons
- **Database Check:** New record in materials table with correct course_id, file_name, file_path, created_at
- **File System Check:** File saved in public/uploads/materials/
- **Pass/Fail:**

### Test Case 1.2: Admin Upload Valid File
- **Steps:** Same as 1.1 but login as admin, use admin dashboard
- **Expected Result:** Same as 1.1
- **Pass/Fail:**

### Test Case 1.3: Invalid File Type Upload
- **Steps:**
  1. Login as teacher/admin
  2. Go to upload page
  3. Select invalid file (e.g., .exe)
  4. Click Upload
- **Expected Result:** Error message: "The file type you are attempting to upload is not allowed."
- **Database Check:** No new record added
- **Pass/Fail:**

### Test Case 1.4: File Too Large Upload
- **Steps:** Select file >10MB, upload
- **Expected Result:** Error message about file size
- **Pass/Fail:**

### Test Case 1.5: No File Selected
- **Steps:** Submit upload form without selecting file
- **Expected Result:** Error message: "The File field is required."
- **Pass/Fail:**

### Test Case 1.6: Multiple File Types
- **Steps:** Upload PDF, DOCX, PPT, JPG, PNG
- **Expected Result:** All upload successfully
- **Pass/Fail:**

## 2. View Functionality

### Test Case 2.1: Teacher Dashboard Materials List
- **Steps:**
  1. Login as teacher
  2. View teacher dashboard
- **Expected Result:** Courses listed with "Upload Material" button; if materials uploaded, they appear in upload page list
- **UI Check:** Clean Bootstrap design, responsive layout
- **Pass/Fail:**

### Test Case 2.2: Admin Dashboard Materials List
- **Steps:** Same as 2.1 for admin
- **Expected Result:** All courses shown with "Upload Material" button
- **Pass/Fail:**

### Test Case 2.3: Student Course View Materials List
- **Steps:**
  1. Login as enrolled student
  2. Go to course view page (/course/view/{id})
- **Expected Result:** Materials listed with Download button only (no Delete)
- **Pass/Fail:**

### Test Case 2.4: Empty Materials List
- **Steps:** View upload page for course with no materials
- **Expected Result:** Message: "No materials uploaded yet for this course."
- **Pass/Fail:**

## 3. Download Functionality

### Test Case 3.1: Enrolled Student Download
- **Steps:**
  1. Login as enrolled student
  2. Go to course view or upload page (if accessible)
  3. Click Download for a material
- **Expected Result:** File downloads with correct name
- **Pass/Fail:**

### Test Case 3.2: Non-Enrolled Student Download Attempt
- **Steps:** Login as student not enrolled, try to download
- **Expected Result:** Error message: "Access denied: You must be enrolled in the course to download materials."
- **Pass/Fail:**

### Test Case 3.3: Non-Logged User Download Attempt
- **Steps:** Without login, access /materials/download/{id}
- **Expected Result:** Redirect to login page
- **Pass/Fail:**

### Test Case 3.4: Invalid Material ID Download
- **Steps:** Access /materials/download/999 (non-existent ID)
- **Expected Result:** 404 error or "Material not found"
- **Pass/Fail:**

## 4. Delete Functionality

### Test Case 4.1: Teacher Delete Own Course Material
- **Steps:**
  1. Login as teacher
  2. Go to upload page for own course
  3. Click Delete, confirm
- **Expected Result:** Material removed from list, success message, file deleted from server
- **Database Check:** Record deleted from materials table
- **Pass/Fail:**

### Test Case 4.2: Admin Delete Any Material
- **Steps:** Same as 4.1 but as admin for any course
- **Expected Result:** Same
- **Pass/Fail:**

### Test Case 4.3: Teacher Delete Other Teacher's Material
- **Steps:** Teacher tries to delete material from another teacher's course
- **Expected Result:** Error: "Access denied: You can only delete materials from your own courses."
- **Pass/Fail:**

### Test Case 4.4: Student Delete Attempt
- **Steps:** Student tries to delete
- **Expected Result:** Access denied, redirect
- **Pass/Fail:**

### Test Case 4.5: Non-Logged User Delete Attempt
- **Steps:** Without login, access /materials/delete/{id}
- **Expected Result:** Redirect to login
- **Pass/Fail:**

## 5. Permissions and Security

### Test Case 5.1: Role-Based Access to Upload Page
- **Steps:** Try accessing /materials/upload/{id} as admin, teacher, student, guest
- **Expected Result:** Admin/teacher: access allowed; student/guest: access denied, redirect
- **Pass/Fail:**

### Test Case 5.2: Role-Based Access to Delete
- **Steps:** Try /materials/delete/{id} as above
- **Expected Result:** Admin/teacher: allowed if permissions; student/guest: denied
- **Pass/Fail:**

### Test Case 5.3: Role-Based Access to Download
- **Steps:** Try /materials/download/{id} as above
- **Expected Result:** Enrolled student: allowed; others: denied
- **Pass/Fail:**

### Test Case 5.4: CSRF Protection
- **Steps:** Try uploading without CSRF token
- **Expected Result:** Upload fails
- **Pass/Fail:**

### Test Case 5.5: SQL Injection Prevention
- **Steps:** Attempt SQL injection in course_id or material_id
- **Expected Result:** No injection possible, handled safely
- **Pass/Fail:**

## 6. UI and UX

### Test Case 6.1: Responsive Design
- **Steps:** View pages on different screen sizes
- **Expected Result:** Bootstrap layout adapts correctly
- **Pass/Fail:**

### Test Case 6.2: Flash Messages
- **Steps:** Perform actions that trigger success/error messages
- **Expected Result:** Messages display correctly and disappear on refresh
- **Pass/Fail:**

### Test Case 6.3: File Icons and Formatting
- **Steps:** Check material list display
- **Expected Result:** Icons (e.g., fa-file), proper date formatting
- **Pass/Fail:**

## Summary
- Total Test Cases: 25
- Passed: ___
- Failed: ___
- Notes: [Any issues or observations]
