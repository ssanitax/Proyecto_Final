<?php

class Valoracion {
	private $db;

	public function __construct($pdo) {
		$this->db = $pdo;
	}

	/**
	 * Devuelve media y total de valoraciones por cada juego recibido.
	 * Estructura de salida: [juego_id => ['media' => float, 'total' => int]]
	 */
	public function obtenerResumenPorJuegos($juegoIds) {
		if (empty($juegoIds)) {
			return [];
		}

		$juegoIds = array_values(array_unique(array_map('intval', $juegoIds)));
		$placeholders = implode(',', array_fill(0, count($juegoIds), '?'));

				$sql = "SELECT ultimas.juego_id,
											 ROUND(AVG(ultimas.valoracion_personal), 1) AS media,
											 COUNT(*) AS total
								FROM (
										SELECT e.juego_id, cu.usuario_id, cu.valoracion_personal
										FROM coleccion_usuario cu
										JOIN ediciones e ON e.id = cu.edicion_id
										JOIN (
												SELECT cu2.usuario_id, e2.juego_id, MAX(cu2.id) AS ultimo_id
												FROM coleccion_usuario cu2
												JOIN ediciones e2 ON e2.id = cu2.edicion_id
												WHERE cu2.valoracion_personal IS NOT NULL
													AND e2.juego_id IN ($placeholders)
												GROUP BY cu2.usuario_id, e2.juego_id
										) dedupe ON dedupe.ultimo_id = cu.id
								) ultimas
								GROUP BY ultimas.juego_id";

		$stmt = $this->db->prepare($sql);
		$stmt->execute($juegoIds);

		$resumen = [];
		foreach ($stmt->fetchAll() as $fila) {
			$resumen[(int)$fila->juego_id] = [
				'media' => (float)$fila->media,
				'total' => (int)$fila->total
			];
		}

		return $resumen;
	}

	/**
	 * Devuelve el resumen de un juego concreto:
	 * - media global
	 * - total valoraciones globales
	 * - media del usuario actual para ese juego
	 */
	public function obtenerResumenJuegoParaUsuario($juegoId, $usuarioId) {
				$sql = "SELECT ROUND(AVG(ultimas.valoracion_personal), 1) AS media_global,
											 COUNT(*) AS total_valoraciones,
											 MAX(CASE WHEN ultimas.usuario_id = :usuario_id THEN ultimas.valoracion_personal END) AS media_usuario
								FROM (
										SELECT cu.usuario_id, cu.valoracion_personal
										FROM coleccion_usuario cu
										JOIN (
												SELECT cu2.usuario_id, MAX(cu2.id) AS ultimo_id
												FROM coleccion_usuario cu2
												JOIN ediciones e2 ON e2.id = cu2.edicion_id
												WHERE e2.juego_id = :juego_id
													AND cu2.valoracion_personal IS NOT NULL
												GROUP BY cu2.usuario_id
										) dedupe ON dedupe.ultimo_id = cu.id
								) ultimas";

		$stmt = $this->db->prepare($sql);
		$stmt->execute([
			':usuario_id' => (int)$usuarioId,
			':juego_id' => (int)$juegoId
		]);

		return $stmt->fetch();
	}
}