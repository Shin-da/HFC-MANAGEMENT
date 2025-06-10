# HFC Management System

## Copyright and Academic Integrity Notice
© 2025 Jeffmathew D. Garcia, Vince Ruel V. Juan, Mike Louie M. Orbe. All Rights Reserved.

This system was developed as an original capstone research project for A Capstone Project Presented to the Faculty of the College of Information Technology and Computer Studies Pamantasan ng Lungsod ng Muntinlupa.

**Authors:**
- GARCIA, JEFFMATHEW D.
- JUAN, VINCE RUEL V.
- ORBE, MIKE LOUIE M.

**IMPORTANT:**
- This work is protected by copyright law and academic integrity policies
- Unauthorized copying, reproduction, or plagiarism of this work is strictly prohibited
- This includes, but is not limited to:
  - Direct copying of code
  - Reproducing documentation
  - Reusing database structures
  - Copying system architecture
  - Replicating business logic
- Academic use must properly cite this work
- Commercial use or redistribution is not permitted without explicit written permission

For academic citations, please use:
```
Garcia, J. D., Juan, V. R. V., & Orbe, M. L. M. (2025). "AN ONLINE SALES AND INVENTORY MANAGEMENT SYSTEM WITH DESCRIPTIVE AND PRESCRIPTIVE ANALYTIC REPORT FOR HENRICH FOOD CORPORATION." A Capstone Project Presented to the Faculty of the College of Information Technology and Computer Studies, Pamantasan ng Lungsod ng Muntinlupa.
```

## Project Overview
This comprehensive management system for Henrich Food Corporation was developed as a capstone research project. The system addresses the need for integrated business process management in the food industry, combining traditional ERP functionalities with modern e-commerce capabilities.

### Research Objectives
1. Develop and implement a custom-designed Online Sales and Inventory Management System (OSIMS) to improve operational efficiency, data accuracy, and internal communication at Henrich Food Corporation.
	* Automatic stock level updates on charts and graphs based on transactions
	* Notifications for low stock, new orders, and order statuses	* Accept order placement and tracking
	* Able to show sales and inventory reports
	* Provision of dashboard monitoring for real-time supervision

## Features

### Admin Dashboard
- Complete administrative control panel
- User management and role-based access control
- System configuration and settings
- Analytics and reporting tools
- Activity logging and monitoring

### Online Shop System
- Product catalog management
- Shopping cart functionality
- Secure checkout process
- Order tracking and management
- Customer account management
- Product image management
- Real-time inventory sync

### Employee Management
- HR data management
- Employee profiles and records
- Attendance tracking
- Performance monitoring
- Leave management
- Document management

### CEO Dashboard
- Executive-level KPI tracking
- Real-time business metrics
- Financial reporting
- Strategic planning tools
- Performance analytics

### Inventory Management
- Stock level tracking
- Product categorization
- Automated reorder points
- Supplier management
- Inventory analytics
- Barcode integration

### Security Features
- Role-based access control (RBAC)
- Secure authentication system
- Session management
- Activity logging
- Data encryption
- XSS and CSRF protection

## Current Limitations and Proposed Improvements

### System Performance
- Database query optimization needed for large datasets
- Caching implementation for frequently accessed data
- Frontend performance optimization for mobile devices
- API response time improvements needed

### User Interface
- Mobile responsiveness needs enhancement
- User feedback indicates need for simpler navigation
- Form validation feedback could be more intuitive
- Dashboard customization options limited

### Features to Implement
- Advanced search functionality across modules
- Batch processing for bulk operations
- Export functionality for reports
- Email notification system
- Enhanced data visualization
- Regular data backup system

### Security Enhancements
- Implement two-factor authentication
- Enhanced password policies
- Regular security audit logging
- Automated backup system
- Session timeout handling

### Documentation Needs
- API documentation completion
- User manual creation
- System administration guide
- Troubleshooting guide
- Database schema documentation
<<<<<<< HEAD
=======
>>>>>>> 2916286f (Update .gitignore to exclude .history directory)
=======
>>>>>>> 14939e830a09fd3d6d8ff2944a0c12a974972c4b

## Technology Stack
- **Backend:**
  - PHP 7.4+
  - MySQL 5.7+
  - Apache/XAMPP
  - WebSocket Server for real-time features
- **Frontend:**
  - HTML5/CSS3
  - JavaScript (ES6+)
  - Bootstrap
  - jQuery
- **Tools & Libraries:**
  - Composer for dependency management
<<<<<<< HEAD
  - Git for version control  - WebSocket for real-time features
=======
  - Git for version control
  - WebSocket for real-time features
>>>>>>> 14939e830a09fd3d6d8ff2944a0c12a974972c4b

## Research Methodology
This project follows a systematic development approach:
1. Requirements Analysis
   - Stakeholder interviews
   - Process mapping
   - System requirements specification
2. System Design
   - Architecture planning
   - Database design
   - UI/UX prototyping
3. Implementation
   - Iterative development
   - Testing
   - User feedback integration
4. Evaluation
   - System testing
   - User acceptance testing
   - Performance evaluation
<<<<<<< HEAD
=======
>>>>>>> 2916286f (Update .gitignore to exclude .history directory)
=======
>>>>>>> 14939e830a09fd3d6d8ff2944a0c12a974972c4b

## Project Structure
```
├── Henrich/                 # Core management system
│   ├── admin/              # Admin module
│   ├── api/                # API endpoints
│   ├── components/         # Reusable components
│   ├── database/          # Database scripts and migrations
│   ├── includes/          # Common includes
│   └── supervisor/        # Supervisor module
├── Online Shop/            # E-commerce platform
│   ├── api/               # Shop API endpoints
│   └── uploads/           # Product images
├── config/                # Configuration files
├── assets/               # Static resources
└── includes/             # Shared includes
```

## Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/XAMPP server
- Composer for PHP dependencies
- Modern web browser
- Minimum 2GB RAM
- 500MB disk space

## Setup and Installation

### Prerequisites
1. Install XAMPP (with PHP 7.4+)
2. Install Composer
3. Install Git

### Installation Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/Shin-da/HFC-MANAGEMENT.git
   ```
2. Navigate to project directory:
   ```bash
   cd HFCManagement
   ```
3. Install dependencies:
   ```bash
   composer install
   ```
4. Configure environment:
   - Copy `.env.example` to `.env`
   - Update database credentials in `.env`
5. Set up database:
   - Create a new MySQL database
   - Import `dbhenrichfoodcorps.sql`
   - Configure connection in `config/database.php`
6. Set proper permissions:
   - Ensure write permissions for uploads directory
   - Configure server permissions as needed
7. Start the WebSocket server (if using real-time features)

## Configuration
- Database settings: `config/database.php`
- Environment variables: `.env`
- Server configuration: Apache/PHP settings
- WebSocket configuration: `websocket/server/config.php`

## Development Guidelines

### Coding Standards
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Comment complex logic
- Keep functions small and focused
- Write unit tests for critical features

### Git Workflow
1. Create feature branches from `main`
2. Follow conventional commits
3. Submit pull requests for review
4. Merge only after approval

### Security Practices
- Validate all user inputs
- Use prepared statements for SQL
- Implement CSRF protection
- Sanitize output
- Keep dependencies updated
- Regular security audits

## Documentation
Detailed documentation available in:
- `henrichoperation_documentation.md`: Core system documentation
- `onlineshop_documentation.md`: Online shop documentation
- Database schema: `database/documentation/`
- API documentation: `api/documentation/`

## Testing
- Unit tests: `tests/unit/`
- Integration tests: `tests/integration/`
- Run tests: `composer test`

## Maintenance
- Regular database backups
- Log rotation
- Cache clearing
- Session cleanup
- Regular updates
- Security patches

## Support
For technical support:
- Create an issue in the repository
- Contact the development team
- Check documentation

## Academic Documentation
- Full research paper: `documentation/research/capstone_paper.pdf`
- System analysis: `documentation/research/system_analysis.pdf`
- User study results: `documentation/research/user_study.pdf`
- Technical documentation: `documentation/technical/`
- API documentation: `documentation/api/`
<<<<<<< HEAD
=======
>>>>>>> 2916286f (Update .gitignore to exclude .history directory)
=======
>>>>>>> 14939e830a09fd3d6d8ff2944a0c12a974972c4b

## About
This project serves as a comprehensive management solution for Henrich Food Corporation, showcasing full-stack development capabilities in PHP and modern web technologies. Developed with scalability, security, and user experience in mind.
