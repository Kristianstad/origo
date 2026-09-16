-- Create the application database only when it does not already exist.
SELECT 'CREATE DATABASE origo'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'origo')\gexec
