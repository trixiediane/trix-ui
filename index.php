<?php

/**
 * ============================================================
 *  EMPLOYEE MANAGEMENT SYSTEM — Full CRUD in One PHP File
 *  Practice File for Support Engineer Interview Prep
 * ============================================================
 *
 *  HOW TO RUN:
 *  1. Make sure you have PHP and MySQL/MariaDB installed.
 *  2. Create a database called "company_db"
 *     > CREATE DATABASE company_db;
 *  3. Run this file with PHP built-in server:
 *     > php -S localhost:8080 crud_practice.php
 *  4. Open http://localhost:8080 in your browser
 *
 * ============================================================
 *  PROBLEMS TO FIND & FIX (Scroll to the bottom for the list)
 * ============================================================
 */

// ============================================================
// DATABASE CONFIGURATION
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'company_db');

// ============================================================
// DATABASE CONNECTION
// ============================================================

function getConnection()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// ============================================================
// DATABASE SETUP — Creates the table if it doesn't exist
// ============================================================

function setupDatabase()
{
    $conn = getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS employees (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(100) NOT NULL,
        email       VARCHAR(100) UNIQUE NOT NULL,
        department  VARCHAR(50)  NOT NULL,
        salary      DECIMAL(10,2) NOT NULL,
        hired_at    DATE NOT NULL,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $conn->query($sql);

    // Seed some sample data if the table is empty
    $result = $conn->query("SELECT COUNT(*) as count FROM employees");
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        $seeds = [
            ["Maria Santos",   "maria@company.com",  "Engineering", 75000, "2022-03-15"],
            ["Juan dela Cruz", "juan@company.com",   "Finance",     60000, "2021-08-01"],
            ["Ana Reyes",      "ana@company.com",    "HR",          55000, "2023-01-10"],
            ["Carlo Mendoza",  "carlo@company.com",  "Engineering", 80000, "2020-06-20"],
            ["Liza Bautista",  "liza@company.com",   "Marketing",   58000, "2022-11-05"],
        ];

        foreach ($seeds as $s) {
            $stmt = $conn->prepare("INSERT INTO employees (name, email, department, salary, hired_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssds", $s[0], $s[1], $s[2], $s[3], $s[4]);
            $stmt->execute();
        }
    }

    $conn->close();
}

// ============================================================
// CRUD FUNCTIONS
// ============================================================

// READ — Get all employees (with optional search)
function getAllEmployees($search = '')
{
    $conn = getConnection();

    if ($search) {
        $stmt = $conn->prepare("SELECT * FROM employees WHERE name LIKE ? OR department LIKE ?");
        $param = "%" . $search . "%";
        $stmt->bind_param("ss", $param, $param);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM employees ORDER BY created_at DESC");
    }

    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }

    $conn->close();
    return $employees;
}

// READ — Get a single employee by ID
function getEmployeeById($id)
{
    $conn = getConnection();

    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();

    $conn->close();
    return $employee;
}

// CREATE — Add a new employee
function createEmployee($name, $email, $department, $salary, $hired_at)
{
    $conn = getConnection();

    // PROBLEM 2 is here — can you spot it?
    $stmt = $conn->prepare("INSERT INTO employees (name, email, department, salary, hired_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $name, $email, $department, $salary, $hired_at);
    $result = $stmt->execute();

    $conn->close();
    return $result;
}

// UPDATE — Edit an existing employee
function updateEmployee($id, $name, $email, $department, $salary, $hired_at)
{
    $conn = getConnection();

    $stmt = $conn->prepare("UPDATE employees SET name=?, email=?, department=?, salary=?, hired_at=? WHERE id=?");

    // PROBLEM 3 is here — can you spot it?
    $stmt->bind_param("sssdsi", $name, $email, $department, $salary, $hired_at, $id);
    $result = $stmt->execute();

    $conn->close();
    return $result;
}

// DELETE — Remove an employee
function deleteEmployee($id)
{
    $conn = getConnection();

    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    $conn->close();
    return $result;
}

// ============================================================
// INPUT VALIDATION
// ============================================================

function validateEmployeeInput($name, $email, $department, $salary, $hired_at)
{
    $errors = [];

    // PROBLEM 4 is here — can you spot it?
    if (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (empty($department)) {
        $errors[] = "Department is required.";
    }

    if ($salary <= 0) {
        $errors[] = "Salary must be greater than 0.";
    }

    if (empty($hired_at)) {
        $errors[] = "Hire date is required.";
    }

    return $errors;
}

// ============================================================
// REQUEST HANDLING (Router)
// ============================================================

setupDatabase();

$action  = $_GET['action'] ?? 'list';
$message = '';
$errors  = [];
$editEmployee = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($action === 'create') {
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $salary     = (float)($_POST['salary'] ?? 0);
        $hired_at   = trim($_POST['hired_at'] ?? '');

        $errors = validateEmployeeInput($name, $email, $department, $salary, $hired_at);

        if (empty($errors)) {
            if (createEmployee($name, $email, $department, $salary, $hired_at)) {
                $message = "Employee added successfully!";
                $action  = 'list';
            } else {
                $errors[] = "Failed to create employee. Email may already exist.";
            }
        }
    }

    if ($action === 'edit') {
        $id         = (int)($_POST['id'] ?? 0);
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $salary     = (float)($_POST['salary'] ?? 0);
        $hired_at   = trim($_POST['hired_at'] ?? '');

        $errors = validateEmployeeInput($name, $email, $department, $salary, $hired_at);

        if (empty($errors)) {
            if (updateEmployee($id, $name, $email, $department, $salary, $hired_at)) {
                $message = "Employee updated successfully!";
                $action  = 'list';
            } else {
                $errors[] = "Failed to update employee.";
            }
        } else {
            $editEmployee = getEmployeeById($id);
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if (deleteEmployee($id)) {
            $message = "Employee deleted.";
        }
        $action = 'list';
    }
}

// Load edit form data
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = (int)($_GET['id'] ?? 0);
    $editEmployee = getEmployeeById($id);
    if (!$editEmployee) {
        $message = "Employee not found.";
        $action  = 'list';
    }
}

// Load list data
$search    = trim($_GET['search'] ?? '');
$employees = ($action === 'list') ? getAllEmployees($search) : [];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600&display=swap');

        :root {
            --bg: #0d1117;
            --surface: #161b22;
            --surface2: #21262d;
            --border: #30363d;
            --green: #3fb950;
            --blue: #58a6ff;
            --orange: #f78166;
            --yellow: #e3b341;
            --text: #e6edf3;
            --muted: #8b949e;
            --danger: #da3633;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'IBM Plex Sans', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ── HEADER ── */
        header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        header h1 {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 16px;
            font-weight: 600;
            color: var(--green);
            letter-spacing: 0.04em;
        }

        header span {
            color: var(--muted);
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
        }

        /* ── LAYOUT ── */
        .container {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 24px;
        }

        /* ── MESSAGES ── */
        .message {
            background: rgba(63, 185, 80, 0.1);
            border: 1px solid var(--green);
            border-radius: 6px;
            padding: 10px 16px;
            margin-bottom: 20px;
            color: var(--green);
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
        }

        .errors {
            background: rgba(218, 54, 51, 0.1);
            border: 1px solid var(--danger);
            border-radius: 6px;
            padding: 10px 16px;
            margin-bottom: 20px;
        }

        .errors p {
            color: var(--danger);
            font-size: 13px;
        }

        /* ── TOOLBAR ── */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar form {
            display: flex;
            gap: 8px;
        }

        /* ── INPUTS & BUTTONS ── */
        input,
        select {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            padding: 8px 12px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus,
        select:focus {
            border-color: var(--blue);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid transparent;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-primary {
            background: var(--green);
            color: #000;
            border-color: var(--green);
        }

        .btn-primary:hover {
            background: #45d05a;
        }

        .btn-edit {
            background: transparent;
            color: var(--blue);
            border-color: var(--blue);
        }

        .btn-edit:hover {
            background: rgba(88, 166, 255, 0.1);
        }

        .btn-delete {
            background: transparent;
            color: var(--orange);
            border-color: var(--orange);
        }

        .btn-delete:hover {
            background: rgba(247, 129, 102, 0.1);
        }

        .btn-secondary {
            background: transparent;
            color: var(--muted);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            color: var(--text);
            border-color: var(--muted);
        }

        /* ── TABLE ── */
        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--surface2);
        }

        th {
            padding: 12px 16px;
            text-align: left;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 600;
        }

        .badge-eng {
            background: rgba(88, 166, 255, 0.15);
            color: var(--blue);
        }

        .badge-fin {
            background: rgba(227, 179, 65, 0.15);
            color: var(--yellow);
        }

        .badge-hr {
            background: rgba(63, 185, 80, 0.15);
            color: var(--green);
        }

        .badge-mkt {
            background: rgba(247, 129, 102, 0.15);
            color: var(--orange);
        }

        .badge-other {
            background: rgba(139, 148, 158, 0.15);
            color: var(--muted);
        }

        .salary-cell {
            font-family: 'IBM Plex Mono', monospace;
            color: var(--green);
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
            font-family: 'IBM Plex Mono', monospace;
        }

        .empty-state .icon {
            font-size: 36px;
            margin-bottom: 12px;
        }

        /* ── FORM CARD ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        .card-header {
            background: var(--surface2);
            border-bottom: 1px solid var(--border);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h2 {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
        }

        .card-header .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 6px var(--green);
        }

        .card-body {
            padding: 28px 24px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .form-group input,
        .form-group select {
            width: 100%;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        /* ── PROBLEMS SECTION ── */
        .problems-section {
            margin-top: 48px;
            border: 1px solid var(--yellow);
            border-radius: 8px;
            overflow: hidden;
        }

        .problems-header {
            background: rgba(227, 179, 65, 0.1);
            border-bottom: 1px solid var(--yellow);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .problems-header h2 {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 14px;
            font-weight: 600;
            color: var(--yellow);
        }

        .problems-body {
            padding: 24px;
        }

        .problem-item {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 18px 20px;
            margin-bottom: 14px;
        }

        .problem-item:last-child {
            margin-bottom: 0;
        }

        .problem-number {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: var(--orange);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 4px;
        }

        .problem-title {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .problem-hint {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.7;
        }

        .problem-hint code {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 3px;
            padding: 1px 6px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            color: var(--blue);
        }

        .problem-location {
            margin-top: 8px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            color: var(--yellow);
        }

        .tip-box {
            background: rgba(88, 166, 255, 0.07);
            border: 1px solid rgba(88, 166, 255, 0.3);
            border-radius: 6px;
            padding: 14px 18px;
            margin-top: 20px;
            font-size: 13px;
            color: var(--muted);
        }

        .tip-box strong {
            color: var(--blue);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: 1;
            }
        }
    </style>
</head>

<body>

    <header>
        <h1>&#9632; EMS</h1>
        <span>// Employee Management System — CRUD Practice File</span>
    </header>

    <div class="container">

        <?php if ($message): ?>
            <div class="message">✓ <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $e): ?>
                    <p>✗ <?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- ===================== LIST VIEW ===================== -->
        <?php if ($action === 'list'): ?>

            <div class="toolbar">
                <form method="GET" action="">
                    <input type="hidden" name="action" value="list">
                    <input type="text" name="search" placeholder="Search name or dept..." value="<?= htmlspecialchars($search) ?>" style="width:260px;">
                    <button type="submit" class="btn btn-secondary">Search</button>
                    <?php if ($search): ?>
                        <a href="?" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
                <a href="?action=create" class="btn btn-primary">+ Add Employee</a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Salary</th>
                            <th>Hired</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="icon">◯</div>
                                        <div>No employees found.</div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $emp): ?>
                                <?php
                                $dept  = $emp['department'];
                                $badge = match (strtolower($dept)) {
                                    'engineering' => 'badge-eng',
                                    'finance'     => 'badge-fin',
                                    'hr'          => 'badge-hr',
                                    'marketing'   => 'badge-mkt',
                                    default       => 'badge-other'
                                };
                                ?>
                                <tr>
                                    <td style="color:var(--muted);font-family:'IBM Plex Mono',monospace;font-size:12px;"><?= $emp['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($emp['name']) ?></strong></td>
                                    <td style="color:var(--muted)"><?= htmlspecialchars($emp['email']) ?></td>
                                    <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($dept) ?></span></td>
                                    <td class="salary-cell">₱<?= number_format($emp['salary'], 2) ?></td>
                                    <td style="color:var(--muted);font-family:'IBM Plex Mono',monospace;font-size:12px;"><?= $emp['hired_at'] ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="?action=edit&id=<?= $emp['id'] ?>" class="btn btn-edit">Edit</a>
                                            <form method="POST" action="?action=delete" onsubmit="return confirm('Delete <?= htmlspecialchars($emp['name']) ?>?')">
                                                <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                                <button type="submit" class="btn btn-delete">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ===================== CREATE FORM ===================== -->
        <?php elseif ($action === 'create'): ?>

            <div class="card">
                <div class="card-header">
                    <div class="dot"></div>
                    <h2>Add New Employee</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="?action=create">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="e.g. Maria Santos" required>
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="e.g. maria@company.com" required>
                            </div>
                            <div class="form-group">
                                <label>Department</label>
                                <select name="department" required>
                                    <option value="">-- Select --</option>
                                    <?php foreach (['Engineering', 'Finance', 'HR', 'Marketing', 'Operations', 'Sales'] as $d): ?>
                                        <option value="<?= $d ?>" <?= (($_POST['department'] ?? '') === $d) ? 'selected' : '' ?>><?= $d ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Monthly Salary (₱)</label>
                                <input type="number" name="salary" value="<?= htmlspecialchars($_POST['salary'] ?? '') ?>" placeholder="e.g. 65000" min="1" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label>Hire Date</label>
                                <input type="date" name="hired_at" value="<?= htmlspecialchars($_POST['hired_at'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save Employee</button>
                            <a href="?" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ===================== EDIT FORM ===================== -->
        <?php elseif ($action === 'edit' && $editEmployee): ?>

            <div class="card">
                <div class="card-header">
                    <div class="dot" style="background:var(--blue);box-shadow:0 0 6px var(--blue)"></div>
                    <h2>Edit Employee — #<?= $editEmployee['id'] ?></h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="?action=edit">
                        <input type="hidden" name="id" value="<?= $editEmployee['id'] ?>">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($editEmployee['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($editEmployee['email']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Department</label>
                                <select name="department" required>
                                    <option value="">-- Select --</option>
                                    <?php foreach (['Engineering', 'Finance', 'HR', 'Marketing', 'Operations', 'Sales'] as $d): ?>
                                        <option value="<?= $d ?>" <?= ($editEmployee['department'] === $d) ? 'selected' : '' ?>><?= $d ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Monthly Salary (₱)</label>
                                <input type="number" name="salary" value="<?= htmlspecialchars($editEmployee['salary']) ?>" min="1" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label>Hire Date</label>
                                <input type="date" name="hired_at" value="<?= htmlspecialchars($editEmployee['hired_at']) ?>" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Employee</button>
                            <a href="?" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        <?php endif; ?>


        <!-- ============================================================ -->
        <!--  PROBLEMS LIST — Try to find and fix these!                  -->
        <!-- ============================================================ -->

        <div class="problems-section">
            <div class="problems-header">
                <span style="font-size:18px;">⚠</span>
                <h2>Practice Problems — Find &amp; Fix These Bugs</h2>
            </div>
            <div class="problems-body">

                <p style="color:var(--muted);font-size:13px;margin-bottom:20px;">
                    There are <strong style="color:var(--yellow);">7 problems</strong> hidden in this file.
                    Try to find each one, understand why it is a problem, and fix it.
                    Solutions are NOT shown here — figure them out first!
                </p>

                <div class="problem-item">
                    <div class="problem-number">Problem 01 — Security</div>
                    <div class="problem-title">SQL Injection Vulnerability in Search</div>
                    <div class="problem-hint">
                        The search function builds a SQL query by directly inserting user input into the query string.
                        Try typing <code>' OR '1'='1</code> in the search box and see what happens.
                        This is one of the most critical security vulnerabilities in web applications.
                        <br><br>
                        <strong style="color:var(--text);">Hint:</strong> Look at the <code>getAllEmployees()</code> function.
                        How does the rest of the CRUD functions protect against this? Apply the same approach.
                    </div>
                    <div class="problem-location">→ Location: getAllEmployees() function, the $search block</div>
                </div>

                <div class="problem-item">
                    <div class="problem-number">Problem 02 — Bug</div>
                    <div class="problem-title">Wrong Data Type in bind_param for Salary (CREATE)</div>
                    <div class="problem-hint">
                        The <code>bind_param()</code> type string tells MySQL what data type each variable is.
                        The type characters are: <code>s</code>=string, <code>i</code>=integer, <code>d</code>=double/decimal, <code>b</code>=blob.
                        <br><br>
                        <strong style="color:var(--text);">Hint:</strong> Look at the type string in <code>createEmployee()</code>.
                        The salary column is <code>DECIMAL(10,2)</code> in the database. Is the correct type character being used?
                        Compare it with how <code>updateEmployee()</code> handles the same field.
                    </div>
                    <div class="problem-location">→ Location: createEmployee() function, bind_param line</div>
                </div>

                <div class="problem-item">
                    <div class="problem-number">Problem 03 — Bug</div>
                    <div class="problem-title">Wrong bind_param Type String Length in UPDATE</div>
                    <div class="problem-hint">
                        The <code>bind_param()</code> type string must have <strong>exactly one character per variable</strong> being bound.
                        Count the <code>?</code> placeholders in the UPDATE SQL query, then count the characters in the type string.
                        <br><br>
                        <strong style="color:var(--text);">Hint:</strong> The UPDATE query sets 5 fields and has a WHERE clause. How many total <code>?</code> are there?
                        How many characters are in <code>"sssdsi"</code>? Do they match?
                    </div>
                    <div class="problem-location">→ Location: updateEmployee() function, bind_param line</div>
                </div>

                <div class="problem-item">
                    <div class="problem-number">Problem 04 — Bug / Logic Error</div>
                    <div class="problem-title">Validation Allows Empty Name</div>
                    <div class="problem-hint">
                        The name validation only checks if the name is shorter than 2 characters.
                        But what happens if the user submits a blank name (just spaces)?
                        <code>trim()</code> is called before passing to validation — so what does <code>strlen(" ")</code> return after trim?
                        <br><br>
                        <strong style="color:var(--text);">Hint:</strong> What should the condition be to also catch empty strings?
                        There are two ways to fix this — can you think of both?
                    </div>
                    <div class="problem-location">→ Location: validateEmployeeInput() function, name check</div>
                </div>

                <div class="problem-item">
                    <div class="problem-number">Problem 05 — Security / XSS</div>
                    <div class="problem-title">Missing Output Escaping in Table</div>
                    <div class="problem-hint">
                        Cross-Site Scripting (XSS) happens when user-supplied data is printed directly into HTML
                        without being escaped. An attacker could store <code>&lt;script&gt;alert('hacked')&lt;/script&gt;</code>
                        as an employee name and it would execute in every visitor's browser.
                        <br><br>
                        <strong style="color:var(--text);">Hint:</strong> Look at the table rows in the LIST VIEW section.
                        Find the column that is printing a value directly using <code>&lt;?= $emp['...'] ?&gt;</code>
                        without wrapping it in <code>htmlspecialchars()</code>. All other columns already have it — which one is missing?
                    </div>
                    <div class="problem-location">→ Location: LIST VIEW, inside the foreach loop, table cells</div>
                </div>

                <div class="problem-item">
                    <div class="problem-number">Problem 06 — Logic Error</div>
                    <div class="problem-title">Salary Validation Accepts Zero</div>
                    <div class="problem-hint">
                        Look carefully at the salary validation condition. It should reject salaries of zero or below.
                        But what value does PHP cast an empty string <code>""</code> to when you do <code>(float)""</code>?
                        And does the current condition actually catch that case?
                        <br><br>
                        <strong style="color:var(--text);">Hint:</strong> What is the result of <code>(float)"" &lt;= 0</code> in PHP?
                        Try it. Now what about <code>(float)"abc" &lt;= 0</code>?
                        Should the validation also check if salary is a valid number using <code>is_numeric()</code>?
                    </div>
                    <div class="problem-location">→ Location: validateEmployeeInput() function, salary check</div>
                </div>

                <div class="problem-item">
                    <div class="problem-number">Problem 07 — Best Practice / Resource Leak</div>
                    <div class="problem-title">Database Connection Not Closed on Error Path</div>
                    <div class="problem-hint">
                        In <code>getEmployeeById()</code>, what happens to the database connection if
                        <code>$stmt->execute()</code> throws an exception or the function returns early?
                        The <code>$conn->close()</code> at the bottom might never be reached.
                        <br><br>
                        <strong style="color:var(--text);">Hint:</strong> In production systems this causes connection pool exhaustion over time —
                        the same type of issue in Problem 8 of your Linux guide!
                        Look into PHP's <code>try / finally</code> block.
                        A <code>finally</code> block always runs, even if an exception is thrown.
                    </div>
                    <div class="problem-location">→ Location: getEmployeeById() function</div>
                </div>

                <div class="tip-box">
                    <strong>Interview Tip:</strong> When reviewing code in a real interview or on the job,
                    always check for these in order: <strong>SQL Injection → XSS → Logic errors → Type mismatches → Resource leaks.</strong>
                    Security bugs first, then correctness bugs, then best practices.
                    These are exactly the kinds of issues a Support / Application Engineer catches during code reviews and incident investigations.
                </div>

            </div>
        </div>

    </div><!-- /container -->

</body>

</html>