<?php

require_once __DIR__ . '/../Services/Database.php';

class AdminController
{
    private static function authorize()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin_approver') {
            die("Unauthorized");
        }
    }

    // =========================
    // DEPARTMENTS
    // =========================
    public static function departments()
    {
        self::authorize();

        $db = Database::connect();
        $departments = $db->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/admin_departments.php';
    }

    public static function storeDepartment()
    {
        self::authorize();

        $db = Database::connect();

        $stmt = $db->prepare("INSERT INTO departments (name) VALUES (:name)");
        $stmt->execute(['name' => $_POST['name']]);

        header("Location: /leave-system/public/admin/departments");
        exit;
    }

    public static function deleteDepartment($id)
    {
        self::authorize();

        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM departments WHERE id = :id");
        $stmt->execute(['id' => $id]);

        header("Location: /leave-system/public/admin/departments");
        exit;
    }

    // =========================
    // JOB TITLES
    // =========================
    public static function jobTitles()
    {
        self::authorize();

        $db = Database::connect();
        $jobTitles = $db->query("SELECT * FROM job_titles ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/admin_jobtitles.php';
    }

    public static function storeJobTitle()
    {
        self::authorize();

        $db = Database::connect();

        $stmt = $db->prepare("INSERT INTO job_titles (name) VALUES (:name)");
        $stmt->execute(['name' => $_POST['name']]);

        header("Location: /leave-system/public/admin/job-titles");
        exit;
    }

    public static function deleteJobTitle($id)
    {
        self::authorize();

        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM job_titles WHERE id = :id");
        $stmt->execute(['id' => $id]);

        header("Location: /leave-system/public/admin/job-titles");
        exit;
    }
}
