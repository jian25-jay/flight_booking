-- Seed Sample Data for Philippine Flight Booking System
-- Run this after creating the database schema

-- Insert Aircraft Types
INSERT INTO aircraft_types (model_name, manufacturer, total_seats) VALUES
('Boeing 737-800', 'Boeing', 180),
('Airbus A320-200', 'Airbus', 195),
('ATR 72-600', 'ATR', 78),
('Bombardier CRJ-900', 'Bombardier', 90),
('Boeing 777-300ER', 'Boeing', 370);

-- Insert Philippine Domestic Routes
INSERT INTO routes (departure_city, arrival_city, flight_duration, distance_km, is_active) VALUES
('Manila', 'Cebu', 90, 600, 1),
('Manila', 'Davao', 150, 900, 1),
('Cebu', 'Davao', 100, 300, 1),
('Manila', 'Iloilo', 75, 350, 1),
('Manila', 'Cagayan de Oro', 120, 700, 1),
('Cebu', 'Iloilo', 60, 180, 1),
('Manila', 'Bacolod', 80, 400, 1),
('Cebu', 'Bacolod', 45, 150, 1),
('Manila', 'Puerto Princesa', 180, 800, 1),
('Manila', 'Tacloban', 100, 500, 1);

-- Insert Sample Flights (for today and next 7 days)
-- Manila to Cebu
INSERT INTO flights (flight_number, route_id, aircraft_type_id, departure_time, arrival_time, base_price, available_seats, total_seats, is_active) VALUES
('PR101', 1, 1, DATE_ADD(CURDATE(), INTERVAL 8 HOUR), DATE_ADD(CURDATE(), INTERVAL 9 HOUR 30 MINUTE), 3500.00, 180, 180, 1),
('PR102', 1, 1, DATE_ADD(CURDATE(), INTERVAL 12 HOUR), DATE_ADD(CURDATE(), INTERVAL 13 HOUR 30 MINUTE), 3500.00, 180, 180, 1),
('PR103', 1, 2, DATE_ADD(CURDATE(), INTERVAL 16 HOUR), DATE_ADD(CURDATE(), INTERVAL 17 HOUR 30 MINUTE), 3800.00, 195, 195, 1);

-- Manila to Davao
INSERT INTO flights (flight_number, route_id, aircraft_type_id, departure_time, arrival_time, base_price, available_seats, total_seats, is_active) VALUES
('PR201', 2, 1, DATE_ADD(CURDATE(), INTERVAL 9 HOUR), DATE_ADD(CURDATE(), INTERVAL 11 HOUR 30 MINUTE), 5000.00, 180, 180, 1),
('PR202', 2, 2, DATE_ADD(CURDATE(), INTERVAL 14 HOUR), DATE_ADD(CURDATE(), INTERVAL 16 HOUR 30 MINUTE), 5200.00, 195, 195, 1);

-- Cebu to Davao
INSERT INTO flights (flight_number, route_id, aircraft_type_id, departure_time, arrival_time, base_price, available_seats, total_seats, is_active) VALUES
('PR301', 3, 3, DATE_ADD(CURDATE(), INTERVAL 7 HOUR), DATE_ADD(CURDATE(), INTERVAL 8 HOUR 40 MINUTE), 2800.00, 78, 78, 1),
('PR302', 3, 4, DATE_ADD(CURDATE(), INTERVAL 13 HOUR), DATE_ADD(CURDATE(), INTERVAL 14 HOUR 40 MINUTE), 3000.00, 90, 90, 1);

-- Manila to Iloilo
INSERT INTO flights (flight_number, route_id, aircraft_type_id, departure_time, arrival_time, base_price, available_seats, total_seats, is_active) VALUES
('PR401', 4, 3, DATE_ADD(CURDATE(), INTERVAL 10 HOUR), DATE_ADD(CURDATE(), INTERVAL 11 HOUR 15 MINUTE), 2500.00, 78, 78, 1),
('PR402', 4, 1, DATE_ADD(CURDATE(), INTERVAL 15 HOUR), DATE_ADD(CURDATE(), INTERVAL 16 HOUR 15 MINUTE), 2600.00, 180, 180, 1);

-- Manila to Cagayan de Oro
INSERT INTO flights (flight_number, route_id, aircraft_type_id, departure_time, arrival_time, base_price, available_seats, total_seats, is_active) VALUES
('PR501', 5, 1, DATE_ADD(CURDATE(), INTERVAL 8 HOUR), DATE_ADD(CURDATE(), INTERVAL 10 HOUR), 4000.00, 180, 180, 1),
('PR502', 5, 2, DATE_ADD(CURDATE(), INTERVAL 13 HOUR), DATE_ADD(CURDATE(), INTERVAL 15 HOUR), 4200.00, 195, 195, 1);

-- Insert Sample Admin User
-- Password: 'admin123' (hashed with bcrypt)
INSERT INTO users (first_name, last_name, email, password_hash, phone, role, is_active) VALUES
('Admin', 'User', 'admin@flightbooking.local', '$2y$10$BQJcXJ.GqKL.UHVsF8k.zOQNOxKpLrJIYkMGQKqTVxL7KNZ5ZPaYe', '+63-9000000000', 'admin', 1);

-- Insert Sample Regular Users
INSERT INTO users (first_name, last_name, email, password_hash, phone, id_number, role, is_active) VALUES
('Juan', 'Dela Cruz', 'juan@example.com', '$2y$10$BQJcXJ.GqKL.UHVsF8k.zOQNOxKpLrJIYkMGQKqTVxL7KNZ5ZPaYe', '+63-9123456789', 'ID-12345678', 'customer', 1),
('Maria', 'Garcia', 'maria@example.com', '$2y$10$BQJcXJ.GqKL.UHVsF8k.zOQNOxKpLrJIYkMGQKqTVxL7KNZ5ZPaYe', '+63-9987654321', 'ID-87654321', 'customer', 1),
('Jose', 'Santos', 'jose@example.com', '$2y$10$BQJcXJ.GqKL.UHVsF8k.zOQNOxKpLrJIYkMGQKqTVxL7KNZ5ZPaYe', '+63-9555555555', 'ID-55555555', 'customer', 1);

-- Insert Admin Settings
INSERT INTO admin_settings (setting_key, setting_value, description) VALUES
('app_name', 'Philippine Flight Booking System', 'Application name'),
('currency', 'PHP', 'Default currency'),
('payment_method', 'mock', 'Default payment method (mock or real gateway)'),
('booking_expiry_hours', '24', 'Hours before pending booking is cancelled'),
('max_passengers_per_booking', '9', 'Maximum passengers per booking'),
('notification_email', 'support@flightbooking.local', 'Email for notifications');

-- Insert Sample Booking (optional - for testing)
-- INSERT INTO bookings (booking_reference, user_id, flight_id, num_passengers, total_price, booking_status) VALUES
-- ('BK12345678', 1, 1, 2, 7000.00, 'confirmed');

-- Insert Sample Passengers for the booking (if you uncomment booking above, uncomment this too)
-- INSERT INTO passengers (booking_id, first_name, last_name, id_type, id_number, seat_number) VALUES
-- (1, 'John', 'Doe', 'Passport', 'AB123456', 'SEAT001'),
-- (1, 'Jane', 'Doe', 'National ID', '12-1234567-8', 'SEAT002');

-- Insert Sample Payment Record (if you uncomment booking above, uncomment this too)
-- INSERT INTO payment_history (booking_id, amount, payment_method, payment_status, transaction_id) VALUES
-- (1, 7000.00, 'mock_payment', 'completed', 'TXN123ABC456DEF');
