ALTER TABLE `demande_pret` 
MODIFY COLUMN `status` ENUM('pending', 'accepted', 'rejected', 'returned', 'cancelled') 
NOT NULL DEFAULT 'pending';