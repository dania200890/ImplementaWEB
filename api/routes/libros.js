const express = require('express');
const router = express.Router();
const usuarioController = require('../controllers/usuarioController');
const libroController = require('../controllers/libroController');

router.post('/login', usuarioController.login); // Ruta para login

router.get('/', libroController.getAllLibros);
router.get('/:id', libroController.getLibro);
 
router.post('/', libroController.createLibro);
router.put('/:id', libroController.updateLibro);
router.delete('/:id', libroController.deleteLibro);

module.exports = router;
