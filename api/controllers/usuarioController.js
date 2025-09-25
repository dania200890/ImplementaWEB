const db = require('../db');

exports.login = async (req, res) => {
    const { usuario, password } = req.body;
    try {
        const [rows] = await db.query('SELECT * FROM usuarios WHERE usuario = ? AND password = ?', [usuario, password]);
        if (rows.length === 0) {
            return res.status(401).json({ message: 'Credenciales inválidas' });
        }

        // Login exitoso
        
        return res.status(200).json({ message: 'Login exitoso', usuario: rows[0] });
    } catch (error) {
        console.error('Error en login:', error);
        return res.status(500).json({ message: 'Error interno del servidor' });
    }
};
// Cerrar sesión
exports.logout = (req, res) => {
    req.session.destroy(err => {
        if (err) {
            console.error('Error al cerrar sesión:', err);
            return res.status(500).json({ message: 'Error interno del servidor' });
        }
        return res.status(200).json({ message: 'Sesión cerrada exitosamente' });
    });
};