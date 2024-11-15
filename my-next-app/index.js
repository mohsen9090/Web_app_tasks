const express = require('express'); const mysql = 
require('mysql2/promise'); const dotenv = 
require('dotenv'); const http = require('http'); 
dotenv.config(); const app = express(); const 
PORT = process.env.PORT || 3006; // Use port 3006 
app.use(express.json());
// Database connection function
async function initializeDatabase() { const 
    connection = await mysql.createConnection({
        host: process.env.DB_HOST, user: 
        process.env.DB_USER, password: 
        process.env.DB_PASSWORD, database: 
        process.env.DB_NAME,
    });
    return connection;
}
// Route to add a new task
app.post('/api/tasks', async (req, res) => { 
    const connection = await 
    initializeDatabase(); const { user_id, 
    task_description, priority = 'medium' } = 
    req.body; // Default priority to 'medium' try 
    {
        const [result] = await 
        connection.execute(
            "INSERT INTO tasks (user_id, 
            task_description, priority, status, 
            created_at, updated_at) VALUES (?, ?, 
            ?, 'pending', NOW(), NOW())", 
            [user_id, task_description, priority]
        ); res.status(201).json({ message: 'Task 
        created successfully!', taskId: 
        result.insertId });
    } catch (error) {
        console.error('Error adding task:', 
        error); res.status(500).json({ message: 
        'Internal server error' });
    } finally {
        await connection.end();
    }
});
// Route to retrieve all tasks
app.get('/api/tasks', async (req, res) => { const 
    connection = await initializeDatabase(); try 
    {
        const [rows] = await 
        connection.execute("SELECT * FROM 
        tasks"); res.json(rows);
    } catch (error) {
        console.error('Error retrieving tasks:', 
        error); res.status(500).json({ message: 
        'Internal server error' });
    } finally {
        await connection.end();
    }
});
// Route to update a task by ID
app.put('/api/tasks/:id', async (req, res) => { 
    const { id } = req.params; const { 
    task_description, priority, status } = 
    req.body; // Allow updating task description, 
    priority, and status const connection = await 
    initializeDatabase(); try {
        const [result] = await 
        connection.execute(
            "UPDATE tasks SET task_description = 
            ?, priority = ?, status = ?, 
            updated_at = NOW() WHERE id = ?", 
            [task_description, priority, status, 
            id]
        ); if (result.affectedRows > 0) { 
            res.json({ message: 'Task updated 
            successfully!' });
        } else {
            res.status(404).json({ message: 'Task 
            not found' });
        }
    } catch (error) {
        console.error('Error updating task:', 
        error); res.status(500).json({ message: 
        'Internal server error' });
    } finally {
        await connection.end();
    }
});
// Route to delete a task by ID
app.delete('/api/tasks/:id', async (req, res) => 
{
    const { id } = req.params; const connection = 
    await initializeDatabase(); try {
        const [result] = await 
        connection.execute("DELETE FROM tasks 
        WHERE id = ?", [id]); if 
        (result.affectedRows > 0) {
            res.status(204).send(); // Task 
            deleted successfully
        } else {
            res.status(404).json({ message: 'Task 
            not found' });
        }
    } catch (error) {
        console.error('Error deleting task:', 
        error); res.status(500).json({ message: 
        'Internal server error' });
    } finally {
        await connection.end();
    }
});
// Start the server
http.createServer(app).listen(PORT, () => { 
    console.log(`Server is running on 
    http://localhost:${PORT}`);
});
