const db = require('../db');

// Obtener todos los libros
exports.getAllLibros = async (req, res) => {
  try {
    
    const [rows] = await db.query('call listarLibros(?,?)', ['admin','admin']);
    if (rows.length === 0) return res.status(404).json({ message: 'No hay libros disponibles' });
    console.log(rows);
    res.json(rows[0]);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Obtener un libro por ID
exports.getLibro = async (req, res) => {
  try {
    const [rows] = await db.query('SELECT * FROM libro WHERE ISBN = ?', [req.params.id]);
    if (rows.length === 0) return res.status(404).json({ message: 'Libro no encontrado' });
    res.json(rows[0]);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Crear nuevo libro
exports.createLibro = async (req, res) => {
  const { ISBN, titulo, editorial, añoPublicacion, idAutor } = req.body;
  try {
    await db.query('INSERT INTO libro (ISBN, titulo, editorial, añoPublicacion, idAutor) VALUES (?, ?, ?, ?, ?)', 
      [ISBN, titulo, editorial, añoPublicacion, idAutor]);
    res.status(201).json({ message: 'Libro creado' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Actualizar libro
exports.updateLibro = async (req, res) => {
  const { titulo, editorial, añoPublicacion, idAutor } = req.body;
  try {
    const [result] = await db.query('UPDATE libro SET titulo = ?, editorial = ?, añoPublicacion = ?, idAutor = ? WHERE ISBN = ?', 
      [titulo, editorial, añoPublicacion, idAutor, req.params.id]);
    if (result.affectedRows === 0) return res.status(404).json({ message: 'Libro no encontrado' });
    res.json({ message: 'Libro actualizado' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Eliminar libro
exports.deleteLibro = async (req, res) => {
  try {
    const [result] = await db.query('DELETE FROM libro WHERE ISBN = ?', [req.params.id]);
    if (result.affectedRows === 0) return res.status(404).json({ message: 'Libro no encontrado' });
    res.json({ message: 'Libro eliminado' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

//rama ejemplo1
// Obtener libros por autor
exports.getLibrosByAutor = async (req, res) => {
  try {
    const [rows] = await db.query('SELECT * FROM libro WHERE idAutor = ?', [req.params.idAutor]);
    if (rows.length === 0) return res.status(404).json({ message: 'No hay libros para este autor' });
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  } 
};

