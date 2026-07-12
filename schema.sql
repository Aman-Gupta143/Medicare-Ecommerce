CREATE DATABASE IF NOT EXISTS medicare_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medicare_db;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS customer_orders;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS medicines;

CREATE TABLE medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT NOT NULL,
    stock INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    prescription_text TEXT NULL,
    original_file_name VARCHAR(255) NULL,
    stored_file_name VARCHAR(255) NULL,
    file_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_prescriptions_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
);

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customer_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_type VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    medicine_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id) REFERENCES customer_orders(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_order_items_medicine
        FOREIGN KEY (medicine_id) REFERENCES medicines(id)
        ON DELETE CASCADE
);

INSERT INTO medicines (name, category, price, description, stock) VALUES
('Paracetamol', 'Pain Relief', 35.00, 'Common fever and mild pain relief tablet.', 10),
('Ibuprofen', 'Pain Relief', 50.00, 'Relief for inflammation, headache, and body pain.', 10),
('Vitamin C', 'Vitamins', 120.00, 'Daily antioxidant and immunity support tablet.', 10),
('Cough Syrup', 'Cold & Flu', 80.00, 'Soothes cough and throat irritation.', 10),
('Antibiotic', 'Antibiotics', 150.00, 'Doctor-advised infection treatment medicine.', 10),
('Pain Relief Spray', 'Pain Relief', 220.00, 'Quick spray for muscle and joint pain.', 10),
('Azithromycin', 'Antibiotics', 180.00, 'Prescription antibiotic for bacterial infections.', 10),
('Amoxicillin', 'Antibiotics', 165.00, 'Widely used antibiotic capsules.', 10),
('Calcium Plus', 'Bone & Joint', 210.00, 'Bone strength and calcium support supplement.', 10),
('Vitamin D3', 'Vitamins', 190.00, 'Supports bone health and immunity.', 10),
('Zincovit', 'Vitamins', 145.00, 'Multivitamin with zinc for daily wellness.', 10),
('ORS Sachet', 'Digestive Care', 40.00, 'Helps prevent dehydration during weakness.', 10),
('Digene Gel', 'Digestive Care', 95.00, 'Relief from acidity and indigestion.', 10),
('Pantoprazole', 'Digestive Care', 130.00, 'Controls stomach acid and reflux.', 10),
('Cetirizine', 'Allergy', 60.00, 'Helps with sneezing, allergy, and itching.', 10),
('Levocetirizine', 'Allergy', 75.00, 'Anti-allergy tablet for seasonal symptoms.', 10),
('Nasal Drops', 'Cold & Flu', 70.00, 'Helps relieve blocked nose.', 10),
('Steam Inhalant', 'Cold & Flu', 90.00, 'Breathing support for congestion relief.', 10),
('Blood Glucose Strips', 'Diabetes Care', 450.00, 'Test strips for sugar level monitoring.', 10),
('Metformin', 'Diabetes Care', 110.00, 'Common diabetes control medicine.', 10);

INSERT INTO medicines (name, category, price, description, stock) VALUES
('Dolo 650', 'Pain Relief', 45.00, 'Popular tablet for fever and body pain.', 10),
('Combiflam', 'Pain Relief', 55.00, 'Used for pain and swelling relief.', 10),
('Volini Spray', 'Pain Relief', 199.00, 'Spray for instant muscular pain relief.', 10),
('Move Gel', 'Pain Relief', 110.00, 'Gel for joint and back pain relief.', 10),
('Saridon', 'Pain Relief', 35.00, 'Tablet for headache relief.', 10),

('Benadryl Syrup', 'Cold & Flu', 95.00, 'Relief from cough and cold symptoms.', 10),
('Sinarest', 'Cold & Flu', 65.00, 'Used for common cold and blocked nose.', 10),
('Vicks Vaporub', 'Cold & Flu', 85.00, 'Relief for congestion and cold discomfort.', 10),
('Honitus Syrup', 'Cold & Flu', 120.00, 'Herbal cough syrup.', 10),
('N95 Mask Pack', 'Cold & Flu', 250.00, 'Protective mask pack.', 10),

('Becosules', 'Vitamins', 145.00, 'Vitamin B complex supplement.', 10),
('Revital H', 'Vitamins', 320.00, 'Daily multivitamin capsules.', 10),
('Supradyn', 'Vitamins', 280.00, 'Energy and immunity multivitamin.', 10),
('Limcee', 'Vitamins', 90.00, 'Chewable Vitamin C tablets.', 10),
('Shelcal 500', 'Vitamins', 160.00, 'Calcium and Vitamin D supplement.', 10),

('Moxikind', 'Antibiotics', 180.00, 'Broad-spectrum antibiotic capsule.', 10),
('Ciplox', 'Antibiotics', 155.00, 'Used for bacterial infections.', 10),
('Doxycycline', 'Antibiotics', 140.00, 'Prescription antibiotic medicine.', 10),
('Ofloxacin', 'Antibiotics', 165.00, 'Used in bacterial infections.', 10),
('Clavam', 'Antibiotics', 210.00, 'Antibiotic combination tablet.', 10),

('ENO', 'Digestive Care', 35.00, 'Quick relief from acidity.', 10),
('Gelusil', 'Digestive Care', 85.00, 'Relief from gas and indigestion.', 10),
('Loperamide', 'Digestive Care', 70.00, 'Helps control loose motion.', 10),
('Isabgol', 'Digestive Care', 150.00, 'Natural fiber supplement.', 10),
('Pudin Hara', 'Digestive Care', 60.00, 'Herbal digestive drops.', 10),

('Allegra', 'Allergy', 170.00, 'Relief from allergy symptoms.', 10),
('Montair', 'Allergy', 145.00, 'Helps with allergic breathing issues.', 10),
('Avil', 'Allergy', 55.00, 'Anti-allergy tablet.', 10),
('NasoClear Spray', 'Allergy', 130.00, 'Nasal spray for allergy relief.', 10),
('Hydrocortisone Cream', 'Allergy', 95.00, 'Cream for itching and irritation.', 10),

('Accu Check Strips', 'Diabetes Care', 550.00, 'Blood sugar test strips.', 10),
('Sugar Free Gold', 'Diabetes Care', 120.00, 'Sugar substitute powder.', 10),
('Insulin Pen Needles', 'Diabetes Care', 260.00, 'Disposable pen needles.', 10),
('Glimepiride', 'Diabetes Care', 95.00, 'Medicine for sugar control.', 10),
('Diabetic Socks', 'Diabetes Care', 180.00, 'Comfort socks for diabetic care.', 10),

('Stethoscope', 'Heart Care', 899.00, 'Basic diagnostic stethoscope.', 10),
('Rosuvastatin', 'Heart Care', 220.00, 'Used for cholesterol management.', 10),
('Clopidogrel', 'Heart Care', 160.00, 'Doctor-advised heart medicine.', 10),
('Pulse Oximeter', 'Heart Care', 699.00, 'Checks oxygen and pulse levels.', 10),
('Heart Support Capsules', 'Heart Care', 350.00, 'Nutritional support capsules.', 10),

('Dettol Liquid', 'First Aid', 110.00, 'Antiseptic liquid for wounds.', 10),
('Gauze Pads', 'First Aid', 65.00, 'Sterile dressing pads.', 10),
('Medical Tape', 'First Aid', 45.00, 'Tape for securing dressings.', 10),
('Hot Water Bag', 'First Aid', 210.00, 'Pain relief hot water bag.', 10),
('Ice Pack', 'First Aid', 180.00, 'Reusable cooling pack.', 10),

('Face Serum', 'Skin Care', 320.00, 'Skin brightening serum.', 10),
('Moisturizer Cream', 'Skin Care', 240.00, 'Daily hydration cream.', 10),
('Lip Balm', 'Skin Care', 75.00, 'Moisturizing lip care balm.', 10),
('Acne Face Wash', 'Skin Care', 180.00, 'Cleanser for acne-prone skin.', 10),
('Body Lotion', 'Skin Care', 260.00, 'Nourishing body lotion.', 10);