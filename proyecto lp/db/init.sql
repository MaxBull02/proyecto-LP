-- init.sql: crea las tablas necesarias para el proyecto

-- Tablas principales
CREATE DATABASE IF NOT EXISTS datauser;
CREATE TABLE IF NOT EXISTS asistentes (
  id SERIAL PRIMARY KEY,
  nombre_estudiante VARCHAR(200) NOT NULL,
  carrera VARCHAR(200) NOT NULL,
  asistencia_confirmada BOOLEAN DEFAULT FALSE,
  fecha_registro TIMESTAMP NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_asistentes_fecha_registro ON asistentes (fecha_registro);
CREATE INDEX IF NOT EXISTS idx_asistentes_asistencia ON asistentes (asistencia_confirmada);

CREATE TABLE IF NOT EXISTS portfolio (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(200) NOT NULL,
  foto TEXT,
  bio TEXT,
  habilidades TEXT,
  fecha_registro TIMESTAMP NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_portfolio_fecha ON portfolio (fecha_registro);



 