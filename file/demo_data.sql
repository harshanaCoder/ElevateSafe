-- ElevateSafe Demo Dataset
-- Import this after file/database.sql

START TRANSACTION;

-- Optional cleanup (uncomment if you want a fresh demo state)
-- DELETE FROM `breakdowns`;

INSERT INTO `breakdowns`
(`unit_no`, `category`, `nature_of_breakdown`, `work_description`, `inform_date`, `inform_time`, `attendent_date`, `attended_time`, `team`, `submit_date`)
VALUES
('ELEV-01', 'Door System', 'Door not closing fully', 'Adjusted door lock sensor and aligned door track. Tested 20 cycles.', '2025-09-03', '08:35:00', '2025-09-03', '09:10:00', 'Team Alpha', '2025-09-03 09:15:00'),
('ELEV-02', 'Electrical', 'Intermittent power trip', 'Checked breaker panel, tightened loose terminal, verified phase balance.', '2025-09-08', '11:20:00', '2025-09-08', '12:05:00', 'Team Bravo', '2025-09-08 12:12:00'),
('ELEV-03', 'Mechanical', 'Abnormal vibration at startup', 'Lubricated guide rails and replaced worn roller guide.', '2025-09-14', '10:05:00', '2025-09-14', '10:52:00', 'Team Alpha', '2025-09-14 10:58:00'),
('ELEV-01', 'Safety System', 'Emergency alarm not responding', 'Replaced alarm relay module and confirmed emergency alarm operation.', '2025-10-02', '14:40:00', '2025-10-02', '15:35:00', 'Team Delta', '2025-10-02 15:40:00'),
('ELEV-04', 'Electronic Board', 'Controller reboot loop', 'Updated controller firmware and replaced backup battery on board.', '2025-10-09', '09:50:00', '2025-10-09', '11:00:00', 'Team Charlie', '2025-10-09 11:08:00'),
('ELEV-05', 'Hydraulic', 'Slow leveling at floor stops', 'Refilled hydraulic oil and removed air from valve block.', '2025-10-16', '13:05:00', '2025-10-16', '14:20:00', 'Team Echo', '2025-10-16 14:25:00'),
('ELEV-02', 'Door System', 'Door reopen sensor too sensitive', 'Recalibrated sensor threshold and cleaned sensor cover.', '2025-10-25', '16:10:00', '2025-10-25', '16:48:00', 'Team Bravo', '2025-10-25 16:55:00'),
('ELEV-06', 'General Maintenance', 'Cabin light flicker complaint', 'Replaced LED driver and secured wiring harness.', '2025-11-04', '07:55:00', '2025-11-04', '08:30:00', 'Team Alpha', '2025-11-04 08:34:00'),
('ELEV-03', 'Mechanical', 'Noise from traction motor', 'Adjusted motor mount and changed bearing grease.', '2025-11-12', '10:45:00', '2025-11-12', '11:40:00', 'Team Delta', '2025-11-12 11:44:00'),
('ELEV-04', 'Electrical', 'Display panel power loss', 'Replaced fused connector and verified stable voltage output.', '2025-11-21', '15:05:00', '2025-11-21', '15:55:00', 'Team Charlie', '2025-11-21 16:00:00'),
('ELEV-07', 'Safety System', 'Emergency stop switch stiff', 'Cleaned contact block and replaced spring return mechanism.', '2025-12-01', '09:10:00', '2025-12-01', '10:00:00', 'Team Echo', '2025-12-01 10:06:00'),
('ELEV-01', 'Door System', 'Car door edge sensor fault', 'Replaced edge strip and re-tested obstruction detection.', '2025-12-10', '12:30:00', '2025-12-10', '13:25:00', 'Team Alpha', '2025-12-10 13:31:00'),
('ELEV-05', 'Hydraulic', 'Oil temperature warning', 'Cleaned cooler fins and topped fluid to recommended level.', '2025-12-18', '14:15:00', '2025-12-18', '15:10:00', 'Team Bravo', '2025-12-18 15:16:00'),
('ELEV-08', 'Electronic Board', 'Hall call buttons unresponsive', 'Re-seated IO board connectors and replaced damaged ribbon cable.', '2026-01-07', '08:25:00', '2026-01-07', '09:22:00', 'Team Charlie', '2026-01-07 09:28:00'),
('ELEV-02', 'Electrical', 'Main contactor overheating', 'Replaced contactor and measured normal current draw.', '2026-01-15', '11:05:00', '2026-01-15', '12:00:00', 'Team Delta', '2026-01-15 12:07:00'),
('ELEV-06', 'General Maintenance', 'Routine pit inspection finding', 'Removed debris, checked buffers, and tightened mounting bolts.', '2026-02-03', '09:35:00', '2026-02-03', '10:18:00', 'Team Echo', '2026-02-03 10:23:00'),
('ELEV-07', 'Mechanical', 'Brake release delay', 'Adjusted brake air gap and replaced worn brake lining.', '2026-02-14', '13:20:00', '2026-02-14', '14:12:00', 'Team Alpha', '2026-02-14 14:18:00'),
('ELEV-08', 'Door System', 'Landing door lock misalignment', 'Realigned lock keeper and validated interlock continuity.', '2026-02-24', '16:00:00', '2026-02-24', '16:42:00', 'Team Bravo', '2026-02-24 16:48:00'),
('ELEV-03', 'Safety System', 'Fire service mode not engaging', 'Updated logic configuration and tested key switch sequence.', '2026-03-04', '10:10:00', '2026-03-04', '11:05:00', 'Team Delta', '2026-03-04 11:11:00'),
('ELEV-01', 'Electronic Board', 'Intermittent command timeout', 'Replaced communication daughterboard and verified CAN stability.', '2026-03-09', '14:05:00', '2026-03-09', '15:00:00', 'Team Charlie', '2026-03-09 15:06:00');

COMMIT;
