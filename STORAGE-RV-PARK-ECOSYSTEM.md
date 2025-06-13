# Vireo Storage & RV Park Ecosystem Architecture

## 🏕️ Overview

The Vireo Storage & RV Park Ecosystem is a comprehensive WordPress-native solution designed specifically for self-storage facilities, RV parks, marina operators, and campground managers. Built as an affordable alternative to expensive enterprise solutions like SiteLink and RMS.

## 📦 Ecosystem Structure

### Core Plugin (Free)
**WordPress.org Distribution**
- Storage Unit Management
- Basic Reservation System
- Customer Database
- Occupancy Calendar
- Simple Billing
- Unit Availability Tracking
- CSV Import/Export
- REST API Foundation

### Pro Plugin (Commercial)
**Vireo Designs Store Distribution**
- All Core Features
- Advanced Reservation Management
- Automated Billing & Late Fees
- Payment Processing Integration
- Multi-Location Support
- Advanced Analytics & Reporting
- Customer Portal
- Priority Support

### Add-on Ecosystem (Modular Extensions)

#### Gate Access Control Add-on
**Price**: $79/year
**Dependencies**: Pro Plugin Required
**Features**:
- Keypad Access Management
- RFID/Card Access Control
- Gate Log Monitoring
- Visitor Access Codes
- Emergency Override System
- Access Time Restrictions
- Security Alert System
- Integration with Security Cameras

#### Facility Security Add-on
**Price**: $69/year
**Dependencies**: Core Plugin (Works with Free or Pro)
**Features**:
- Security Camera Integration
- Motion Detection Alerts
- Incident Report Management
- 24/7 Monitoring Dashboard
- Security Guard Management
- Alarm System Integration
- Emergency Response Protocols
- Video Storage & Retrieval

#### Utility Management Add-on
**Price**: $59/year
**Dependencies**: Pro Plugin Required
**Features**:
- Electricity Usage Monitoring
- Water/Sewer Tracking
- Propane Level Management
- Utility Billing Automation
- Meter Reading Schedules
- Usage Analytics
- Conservation Alerts
- Multi-Utility Support

#### Maintenance & Cleaning Add-on
**Price**: $49/year
**Dependencies**: Core Plugin (Works with Free or Pro)
**Features**:
- Maintenance Schedule Management
- Cleaning Task Automation
- Work Order System
- Vendor Management
- Equipment Tracking
- Preventive Maintenance
- Facility Inspection Tools
- Cost Tracking & Reporting

#### Customer Communication Add-on
**Price**: $39/year
**Dependencies**: Core Plugin (Works with Free or Pro)
**Features**:
- Automated Email/SMS Notifications
- Payment Reminders
- Facility Announcements
- Weather Alerts
- Emergency Notifications
- Multi-Language Support
- Template Management
- Communication History

#### Mobile App Add-on
**Price**: $89/year
**Dependencies**: Pro Plugin Required
**Features**:
- Native iOS/Android Apps
- Customer Self-Service Portal
- Mobile Payments
- Unit Photos & Documentation
- Offline Functionality
- Push Notifications
- GPS Facility Location
- Staff Management Tools

## 🏗️ Technical Architecture

### Core Infrastructure

```
vireo-storage-rv/
├── vireo-storage-rv.php                   # Main plugin file
├── core/                                  # Free functionality
│   ├── includes/
│   │   ├── admin/
│   │   │   ├── class-vsrv-admin.php
│   │   │   ├── class-vsrv-dashboard.php
│   │   │   ├── class-vsrv-settings.php
│   │   │   └── class-vsrv-list-tables.php
│   │   ├── api/
│   │   │   ├── class-vsrv-rest-api.php
│   │   │   └── endpoints/
│   │   ├── core/
│   │   │   ├── class-vsrv-unit.php
│   │   │   ├── class-vsrv-customer.php
│   │   │   ├── class-vsrv-reservation.php
│   │   │   └── class-vsrv-billing.php
│   │   └── public/
│   │       ├── class-vsrv-public.php
│   │       └── class-vsrv-shortcodes.php
│   └── assets/
├── pro/                                   # Pro functionality
│   ├── includes/
│   │   ├── advanced-reservations/
│   │   ├── payment-processing/
│   │   ├── customer-portal/
│   │   ├── analytics/
│   │   └── licensing/
│   └── assets/
└── addons/                                # Add-on framework
    ├── addon-loader.php
    ├── addon-api.php
    └── interfaces/
```

## 🗄️ Database Schema

### Core Tables

```sql
-- Storage Units/RV Sites
CREATE TABLE vsrv_units (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    unit_number varchar(50) NOT NULL,
    unit_type varchar(50) NOT NULL, -- 'storage', 'rv_site', 'boat_slip', 'parking'
    size_width decimal(8,2),
    size_length decimal(8,2),
    size_height decimal(8,2),
    square_footage decimal(10,2),
    climate_controlled boolean DEFAULT false,
    drive_up_access boolean DEFAULT false,
    ground_floor boolean DEFAULT false,
    security_features text,
    amenities text, -- For RV: hookups, cable, wifi, etc.
    hookup_type varchar(50), -- '30amp', '50amp', 'water_sewer', 'full'
    location_section varchar(50),
    location_row varchar(10),
    monthly_rate decimal(10,2),
    daily_rate decimal(10,2),
    weekly_rate decimal(10,2),
    security_deposit decimal(10,2),
    status varchar(20) DEFAULT 'available', -- 'available', 'occupied', 'reserved', 'maintenance'
    facility_id bigint(20) unsigned,
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unit_facility (unit_number, facility_id),
    KEY unit_type (unit_type),
    KEY status (status),
    KEY facility_id (facility_id)
);

-- Facilities/Locations
CREATE TABLE vsrv_facilities (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    facility_type varchar(50), -- 'self_storage', 'rv_park', 'marina', 'mixed'
    address text,
    city varchar(100),
    state varchar(50),
    zip varchar(20),
    country varchar(50) DEFAULT 'US',
    phone varchar(20),
    email varchar(255),
    manager_name varchar(200),
    operating_hours text,
    latitude decimal(10, 6),
    longitude decimal(10, 6),
    gate_hours text,
    amenities text,
    policies text,
    emergency_contact varchar(200),
    status varchar(20) DEFAULT 'active',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY facility_type (facility_type),
    KEY status (status),
    KEY city_state (city, state)
);

-- Customers
CREATE TABLE vsrv_customers (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    customer_type varchar(20) DEFAULT 'individual', -- 'individual', 'business'
    first_name varchar(100),
    last_name varchar(100),
    company_name varchar(255),
    email varchar(255),
    phone varchar(20),
    alt_phone varchar(20),
    address text,
    city varchar(100),
    state varchar(50),
    zip varchar(20),
    country varchar(50) DEFAULT 'US',
    drivers_license varchar(50),
    emergency_contact_name varchar(200),
    emergency_contact_phone varchar(20),
    emergency_contact_relationship varchar(50),
    credit_limit decimal(10,2),
    account_status varchar(20) DEFAULT 'active',
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY email (email),
    KEY phone (phone),
    KEY account_status (account_status),
    KEY customer_type (customer_type)
);

-- Reservations/Leases
CREATE TABLE vsrv_reservations (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    reservation_number varchar(50) NOT NULL,
    customer_id bigint(20) unsigned NOT NULL,
    unit_id bigint(20) unsigned NOT NULL,
    facility_id bigint(20) unsigned NOT NULL,
    reservation_type varchar(50), -- 'storage', 'rv_monthly', 'rv_daily', 'rv_weekly'
    start_date date NOT NULL,
    end_date date,
    move_in_date date,
    move_out_date date,
    monthly_rate decimal(10,2),
    daily_rate decimal(10,2),
    security_deposit decimal(10,2),
    prorate_amount decimal(10,2),
    status varchar(20) DEFAULT 'active', -- 'active', 'pending', 'terminated', 'expired'
    payment_due_day int(11) DEFAULT 1,
    auto_renew boolean DEFAULT true,
    gate_access_code varchar(20),
    special_instructions text,
    lease_terms text,
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY reservation_number (reservation_number),
    KEY customer_id (customer_id),
    KEY unit_id (unit_id),
    KEY facility_id (facility_id),
    KEY status (status),
    KEY start_date (start_date),
    FOREIGN KEY (customer_id) REFERENCES vsrv_customers(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES vsrv_units(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES vsrv_facilities(id) ON DELETE CASCADE
);

-- Billing/Invoices
CREATE TABLE vsrv_invoices (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    invoice_number varchar(50) NOT NULL,
    reservation_id bigint(20) unsigned NOT NULL,
    customer_id bigint(20) unsigned NOT NULL,
    facility_id bigint(20) unsigned NOT NULL,
    invoice_date date NOT NULL,
    due_date date NOT NULL,
    period_start date,
    period_end date,
    rent_amount decimal(10,2),
    late_fee decimal(10,2),
    other_charges decimal(10,2),
    tax_amount decimal(10,2),
    total_amount decimal(10,2),
    amount_paid decimal(10,2) DEFAULT 0,
    balance_due decimal(10,2),
    status varchar(20) DEFAULT 'pending', -- 'pending', 'paid', 'overdue', 'partial'
    payment_method varchar(50),
    payment_date date,
    payment_reference varchar(100),
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY invoice_number (invoice_number),
    KEY reservation_id (reservation_id),
    KEY customer_id (customer_id),
    KEY facility_id (facility_id),
    KEY status (status),
    KEY due_date (due_date),
    FOREIGN KEY (reservation_id) REFERENCES vsrv_reservations(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES vsrv_customers(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES vsrv_facilities(id) ON DELETE CASCADE
);
```

### Pro Feature Tables

```sql
-- Payments (Pro Feature)
CREATE TABLE vsrv_payments (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    invoice_id bigint(20) unsigned,
    reservation_id bigint(20) unsigned,
    customer_id bigint(20) unsigned NOT NULL,
    facility_id bigint(20) unsigned NOT NULL,
    amount decimal(10,2) NOT NULL,
    payment_date date NOT NULL,
    payment_method varchar(50), -- 'cash', 'check', 'card', 'ach', 'online'
    payment_reference varchar(100),
    transaction_id varchar(100),
    processor_fee decimal(10,2),
    status varchar(20) DEFAULT 'completed',
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY invoice_id (invoice_id),
    KEY reservation_id (reservation_id),
    KEY customer_id (customer_id),
    KEY facility_id (facility_id),
    KEY payment_date (payment_date),
    FOREIGN KEY (invoice_id) REFERENCES vsrv_invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (reservation_id) REFERENCES vsrv_reservations(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES vsrv_customers(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES vsrv_facilities(id) ON DELETE CASCADE
);

-- Access Logs (Pro Feature)
CREATE TABLE vsrv_access_logs (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    customer_id bigint(20) unsigned,
    facility_id bigint(20) unsigned NOT NULL,
    unit_id bigint(20) unsigned,
    access_type varchar(50), -- 'gate', 'unit', 'facility'
    access_method varchar(50), -- 'code', 'card', 'fob', 'manager_override'
    access_code varchar(20),
    access_time datetime NOT NULL,
    exit_time datetime,
    vehicle_info varchar(200),
    access_granted boolean DEFAULT true,
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY customer_id (customer_id),
    KEY facility_id (facility_id),
    KEY unit_id (unit_id),
    KEY access_time (access_time),
    KEY access_type (access_type),
    FOREIGN KEY (customer_id) REFERENCES vsrv_customers(id) ON DELETE SET NULL,
    FOREIGN KEY (facility_id) REFERENCES vsrv_facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES vsrv_units(id) ON DELETE SET NULL
);

-- Maintenance Records (Pro Feature)
CREATE TABLE vsrv_maintenance (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    facility_id bigint(20) unsigned NOT NULL,
    unit_id bigint(20) unsigned,
    maintenance_type varchar(50), -- 'cleaning', 'repair', 'inspection', 'pest_control'
    description text,
    scheduled_date date,
    completed_date date,
    assigned_to varchar(200),
    vendor_name varchar(200),
    cost decimal(10,2),
    recurring_schedule varchar(50), -- 'weekly', 'monthly', 'quarterly', 'annual'
    priority varchar(20) DEFAULT 'normal', -- 'low', 'normal', 'high', 'emergency'
    status varchar(20) DEFAULT 'scheduled', -- 'scheduled', 'in_progress', 'completed', 'cancelled'
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY facility_id (facility_id),
    KEY unit_id (unit_id),
    KEY maintenance_type (maintenance_type),
    KEY status (status),
    KEY scheduled_date (scheduled_date),
    FOREIGN KEY (facility_id) REFERENCES vsrv_facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES vsrv_units(id) ON DELETE SET NULL
);
```

## 🔌 API Structure

### REST API Endpoints

```php
// Core API Endpoints
/wp-json/vsrv/v1/units/
/wp-json/vsrv/v1/units/{id}
/wp-json/vsrv/v1/units/{id}/availability
/wp-json/vsrv/v1/facilities/
/wp-json/vsrv/v1/facilities/{id}
/wp-json/vsrv/v1/customers/
/wp-json/vsrv/v1/customers/{id}
/wp-json/vsrv/v1/reservations/
/wp-json/vsrv/v1/reservations/{id}
/wp-json/vsrv/v1/invoices/
/wp-json/vsrv/v1/search/units

// Pro API Endpoints
/wp-json/vsrv/v1/payments/
/wp-json/vsrv/v1/access-logs/
/wp-json/vsrv/v1/maintenance/
/wp-json/vsrv/v1/analytics/
/wp-json/vsrv/v1/reports/

// Add-on API Extension Points
/wp-json/vsrv/v1/addons/{addon-slug}/
/wp-json/vsrv/v1/gate-access/
/wp-json/vsrv/v1/security/
```

## 💰 Pricing Strategy

### Pricing Tiers
- **Core**: Free (WordPress.org)
- **Pro**: $79/year (Includes all pro features)
- **Pro + 2 Add-ons**: $169/year (25% bundle discount)
- **Pro + All Add-ons**: $249/year (40% bundle discount)

### Individual Add-on Pricing
- **Gate Access Control**: $79/year
- **Facility Security**: $69/year
- **Utility Management**: $59/year
- **Maintenance & Cleaning**: $49/year
- **Customer Communication**: $39/year
- **Mobile App**: $89/year

### Target Customer Segments

#### Tier 1: Small Self-Storage (50-150 units)
- **Solution**: Core + Customer Communication
- **Price**: $39/year
- **Value Prop**: Basic management with automated customer communication

#### Tier 2: Mid-Size Storage/RV Park (150-500 units)
- **Solution**: Pro + Gate Access + Security
- **Price**: $169/year (bundle)
- **Value Prop**: Professional management with security and access control

#### Tier 3: Large Multi-Location Facilities (500+ units)
- **Solution**: Pro + All Add-ons
- **Price**: $249/year (full bundle)
- **Value Prop**: Complete enterprise-class facility management

## 🚀 Competitive Positioning

### vs. SiteLink/U-Haul U-Box Pro
- **Pricing**: $249/year vs $50-150/month per location
- **Setup**: WordPress install vs cloud dependency
- **Customization**: Full WordPress ecosystem vs locked platform
- **Data Ownership**: Complete vs vendor lock-in

### vs. RMS/QuikStor
- **Integration**: Native WordPress vs standalone software
- **Scalability**: WordPress hosting vs software limitations
- **Cost**: One-time annual vs per-unit pricing
- **Flexibility**: Open source core vs proprietary

## 📊 Development Roadmap

### Phase 1: Core Development (Q2 2025)
- **Timeline**: April - June 2025
- **Focus**: Unit management, basic reservations
- **Deliverables**: 
  - Storage unit/RV site management
  - Customer database
  - Basic reservation system
  - Simple billing

### Phase 2: Pro Features (Q3 2025)
- **Timeline**: July - September 2025
- **Focus**: Advanced features, payment processing
- **Deliverables**:
  - Advanced reservation management
  - Payment gateway integration
  - Customer portal
  - Analytics dashboard

### Phase 3: Essential Add-ons (Q4 2025)
- **Priority Add-ons**:
  1. Customer Communication (High demand, low complexity)
  2. Gate Access Control (High value, medium complexity)
  3. Facility Security (Medium demand, medium complexity)

### Phase 4: Advanced Add-ons (Q1 2026)
- **Advanced Add-ons**:
  1. Mobile App (High value, high complexity)
  2. Utility Management (Medium demand, high complexity)
  3. Maintenance & Cleaning (Medium demand, moderate complexity)

## 🎯 Customer Journey

### Discovery
- WordPress.org search for "storage management"
- Industry blog content about facility management
- Self-storage and RV park forums

### Trial
- Install free core plugin
- Experience basic unit management
- See pro feature prompts and upgrade CTAs

### Conversion
- Purchase pro license ($79/year)
- Experience advanced features
- Identify need for specific add-ons

### Expansion
- Add Customer Communication for automated messaging
- Add Gate Access for security and convenience
- Consider additional add-ons as business grows

### Retention
- Regular feature updates
- Priority support
- Industry-specific resources
- Community engagement

## 🛠️ Implementation Strategy

### Development Priorities
1. **Core Stability**: Unit management and basic reservations
2. **Pro Features**: Advanced reservations and payment processing
3. **Communication Add-on**: High-impact, customer-requested feature
4. **Gate Access Add-on**: Security-focused professional feature

### Quality Assurance
- Multi-facility testing
- RV park vs storage facility workflow testing
- Payment processing security testing
- Performance testing with large unit inventories

### Documentation Strategy
- Facility management getting started guide
- Industry best practices guides
- API documentation for integrations
- Video tutorials for complex workflows

### Support Structure
- Community forum for free users
- Priority email support for pro users
- Phone support for enterprise customers
- Industry-specific knowledge base

## 📈 Success Metrics

### Technical KPIs
- Plugin activation rate
- Unit occupancy tracking accuracy
- Reservation system performance
- Payment processing success rate

### Business KPIs
- Free to Pro conversion rate (Target: 8-12%)
- Add-on attach rate (Target: 50% of Pro users)
- Customer lifetime value
- Churn rate (Target: <10% annually)

### Customer Satisfaction
- Facility management efficiency improvement
- Reduced administrative overhead
- Faster customer service
- Revenue optimization tracking

## 🔒 Security & Compliance

### Data Security
- Encrypted customer payment data
- Secure access control integration
- Protected facility security information
- Regular security audits

### Industry Compliance
- Self-storage lien law compliance
- RV park health and safety regulations
- Payment card industry (PCI) compliance
- Data privacy protection

### Backup & Recovery
- Customer data backup
- Facility information protection
- Financial record preservation
- Disaster recovery procedures

---

## 📋 Next Actions

1. **Research Industry Requirements** - Self-storage vs RV park specific needs
2. **Design Core Architecture** - Flexible unit management system
3. **Build Reservation System** - Advanced booking and calendar management
4. **Implement Communication Add-on** - First high-value extension
5. **Develop Gate Access Integration** - Security-focused add-on
6. **Establish Industry Partnerships** - Self-storage associations and RV organizations

This ecosystem architecture provides a comprehensive roadmap for building a profitable storage and RV park management solution that can compete effectively with enterprise platforms while serving the underserved small to medium facility market.

## 🌟 Industry-Specific Features

### Self-Storage Facilities
- Lien sale management
- Climate control monitoring
- Move-in/move-out inspections
- Overlock procedures
- Insurance requirement tracking

### RV Parks & Campgrounds
- Site hookup management (30/50 amp, water, sewer)
- Seasonal vs transient reservations
- Pet policy management
- Activity calendar integration
- Weather alert system

### Marina Operations
- Boat slip management
- Tide and weather integration
- Fuel dock management
- Harbor master functions
- Boat registration tracking

This ecosystem serves multiple related industries with shared core functionality while providing industry-specific extensions through the flexible add-on system.