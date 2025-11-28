<?php

// Prevents Cross SIde Scripting
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function getDB($file) {
    $db_path = __DIR__ . "/../db/" . $file . ".db";
    $pdo = new PDO("sqlite:" . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

// -------------------------------------------------------------------------------------------------------
// Create
// -------------------------------------------------------------------------------------------------------

function addGoals($user, $goal, $type) {
    $user_id = getUserId($user);
    $pdo = getDB('users');

    $stmt = $pdo->prepare("INSERT INTO goals (user_id, week, year, goal_type, target_count, progress_count)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id, date('W'), date('o'), $type, $goal, 0
    ]);
}

function addPerson($user, $name, $contact, $date, $notes, $status) {
    $user_id = getUserId($user);
    $pdo = getDB('users');

    if (!$user_id) {
        throw new Exception("User not found");
    }

    $stmt = $pdo->prepare("
        INSERT INTO people (user_id, name, contact_info, status)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $name,
        $contact,
        $status
    ]);

    addNote($pdo->lastInsertId(), $date, $notes);

    $type = '';
    switch ($status) {
        case 'Conversation':
            $type = 'conversations';
            break;
        case "Connection":
            $type = 'connections';
            break;
        case 'Friendship':
            $type = 'friendships';
            break;
        case 'Date':
            $type = 'dates';
            break;
        default:
            $type = '';
            break;
    }
    incrementGoalProgress($user, date('W'), date('o'), $type, 1);
    return;
}

function addNote($pid, $date, $note) {
    $pdo = getDB('users');

    $stmt = $pdo->prepare("
        INSERT INTO notes (person_id, note, note_date)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $pid,
        $note,
        $date
    ]);
}

// -------------------------------------------------------------------------------------------------------
// Retrieve
// -------------------------------------------------------------------------------------------------------

function getUserId($user) {
    $pdo = getDB('users');

    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$user]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['id'] ?? null;  
}

function getFirstName($user) {
    $pdo = getDB('users');

    $stmt = $pdo->prepare("SELECT first_name FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $fname = $stmt->fetch(PDO::FETCH_ASSOC);
    return $fname['first_name'];
}

function getNotes($pid) {
    $pdo = getDB('users');

    $stmt = $pdo->prepare("SELECT * FROM notes WHERE person_id = ? ORDER BY note_date DESC");
    $stmt->execute([$pid]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $notes;
}

function getLastName($user) {
    $pdo = getDB('users');

    $stmt = $pdo->prepare("SELECT last_name FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $lname = $stmt->fetch(PDO::FETCH_ASSOC);
    return $lname['last_name'];
}

function getPeople($user) {
    $id = getUserId($user);
    $pdo = getDB('users');

    $stmt = $pdo->prepare("SELECT * FROM people WHERE user_id = ?");
    $stmt->execute([$id]);
    $people = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $people;
}

function getPeopleWithStatus($user, $status) {
    $id = getUserId($user);
    $pdo = getDB('users');

    $stmt = $pdo->prepare("SELECT * FROM people WHERE user_id = ? AND status = ?");
    $stmt->execute([$id, $status]);
    $people = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $people;
}

function getPeopleAndNotes($user) {
    $people = getPeople($user);
    $notes = [];
    for ($i = 0; $i < count($people); $i++) {
        $note = getNotes($people[$i]['id']);
        $notes[(string) $people[$i]['id']] = $note;
    }
    return $notes;
}

function readGoals($user, $week, $year, $type) {
    $user_id = getUserId($user);
    $pdo = getDB('users');

    $stmt = $pdo->prepare("SELECT * FROM goals WHERE user_id = ? AND week = ? AND year = ? AND goal_type = ?");
    $stmt->execute([$user_id, $week, $year, $type]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getGoalCount($user, $week, $year, $type, $field) {
    $row = readGoals($user, $week, $year, $type);
    if (isset($row[0])) {
        return $row[0][$field]; // or target_count
    }
    return 0;
}

function getMonthGoals($user, $type, $field) {
    $week4 = date('W');
    $year4 = date('o');

    $week3 = 0;
    $week2 = 0;
    $week1 = 0;

    $year3 = 0;
    $year2 = 0;
    $year1 = 0;

    switch ($week4) {

        case 3:
            $week3 = $week4 - 1;
            $week2 = $week4 - 2;
            $week1 = 52;
            $year3 = $year4;
            $year2 = $year4;
            $year1 = $year4 - 1;
            break;

        case 2:
            $week3 = $week4 - 1;
            $week2 = 52;
            $week1 = 51;
            $year3 = $year4;
            $year2 = $year4 - 1;
            $year1 = $year4 - 1;
            break;

        case 1:
            $week3 = 52;
            $week2 = 51;
            $week1 = 50;
            $year3 = $year4 - 1;
            $year2 = $year4 - 1;
            $year1 = $year4 - 1;
            break;

        case 53:  // optional but correct
            $week3 = 52;
            $week2 = 51;
            $week1 = 50;
            $year3 = $year4;
            $year2 = $year4;
            $year1 = $year4;
            break;

        default:
            $week3 = $week4 - 1;
            $week2 = $week4 - 2;
            $week1 = $week4 - 3;
            $year3 = $year4;
            $year2 = $year4;
            $year1 = $year4;
            break; 
    }

    $res1 = getGoalCount($user, $week1, $year1, $type, $field);
    $res2 = getGoalCount($user, $week2, $year2, $type, $field);
    $res3 = getGoalCount($user, $week3, $year3, $type, $field);
    $res4 = getGoalCount($user, $week4, $year4, $type, $field);

    return [$res1, $res2, $res3, $res4];
}

// -------------------------------------------------------------------------------------------------------
// Update
// -------------------------------------------------------------------------------------------------------

function incrementGoalProgress($user, $week, $year, $type, $amount = 1) {
    $user_id = getUserId($user);
    $pdo = getDB('users');

    $stmt = $pdo->prepare("
        SELECT id, progress_count 
        FROM goals 
        WHERE user_id = ? AND week = ? AND year = ? AND goal_type = ?
    ");
    $stmt->execute([$user_id, $week, $year, $type]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $newCount = $row['progress_count'] + $amount;

        $update = $pdo->prepare("
            UPDATE goals
            SET progress_count = ?
            WHERE id = ?
        ");
        $update->execute([$newCount, $row['id']]);

    } else {
        $insert = $pdo->prepare("
            INSERT INTO goals (user_id, week, year, goal_type, target_count, progress_count)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $insert->execute([$user_id, $week, $year, $type, 0, $amount]);
    }
}

function updateGoals($user, $week, $year, $type, $target) {
    $user_id = getUserId($user);
    $pdo = getDB('users');

    $stmt = $pdo->prepare("
        SELECT id, target_count 
        FROM goals 
        WHERE user_id = ? AND week = ? AND year = ? AND goal_type = ?
    ");
    $stmt->execute([$user_id, $week, $year, $type]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $update = $pdo->prepare("
            UPDATE goals
            SET target_count = ?
            WHERE id = ?
        ");
        $update->execute([$target, $row['id']]);

    } else {

        $insert = $pdo->prepare("
            INSERT INTO goals (user_id, week, year, goal_type, target_count, progress_count)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $insert->execute([$user_id, $week, $year, $type, $target, 0]);
    }
}

function updateStatus($user, $pid, $status) {
    $pdo = getDB('users');
    $stmt = $pdo->prepare("SELECT * FROM people WHERE id = ?");
    $stmt->execute([$pid]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return false;
    }

    $update = $pdo->prepare("
        UPDATE people
        SET status = ?
        WHERE id = ?
    ");
    $update->execute([$status, $row['id']]);

    if($status == "Conversation") {
        incrementGoalProgress($user, date('W'), date('o'), "conversation", 1);
    }
    else if($status == "Connection") {
        incrementGoalProgress($user, date('W'), date('o'), "connections", 1);
    }
    else if($status == "Friendship") {
        incrementGoalProgress($user, date('W'), date('o'), "friendships", 1);
    }
    else if($status == "Dating") {
        incrementGoalProgress($user, date('W'), date('o'), "dates", 1);
    } 

    return true;
}

// -------------------------------------------------------------------------------------------------------
// Delete
// -------------------------------------------------------------------------------------------------------