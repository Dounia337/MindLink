# MindLink - Mental Wellness Platform for Students

##  About MindLink

MindLink is a student-centered mental wellness platform that provides a safe, anonymous space for students to express their feelings, connect with peers, and access mental health resources. Built with empathy at its core, MindLink creates a supportive community where every voice matters and all feelings are valid.

---

##  Key Features

### For All Users
- **Anonymous Posting** - Share thoughts and feelings without revealing your identity
- **Interactive Feed** - View and engage with posts from the community
- **Comments & Likes** - Support others through meaningful interactions
- **User Profiles** - Manage your personal information and profile picture
- **Help Center** - Comprehensive FAQ and support resources

### For Verified Peer Counselors
- **Resource Sharing** - Post mental wellness tips and guidance
- **Special Badge** - Verified counselor status displayed on profile
- **Community Leadership** - Help guide and support fellow students

### For Counselor Admins
- **Peer Counselor Management** - Add and manage verified counselor IDs
- **Platform Statistics** - View total users, posts, and resources
- **Access Control** - Manage verification credentials

---

##  Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **AJAX**: Real-time interactions without page reload
- **Custom Fonts**: Bubblebody Neue

---

## Database Schema

### Tables

1. **Users** - User accounts with role management
2. **Posts** - User-generated content with anonymity option
3. **Comments** - Threaded discussions on posts
4. **Likes** - Post engagement tracking
5. **Resources** - Mental wellness content from counselors
6. **Peer_counselor_ids** - Verification credentials

---

##  Installation & Setup

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Write permissions for uploads directory

### Setup Steps

**1. Clone or download the project files**

**2. Database Configuration**
```sql
-- Create database
CREATE DATABASE mindlink;

-- Import the schema
USE mindlink;
-- Run all CREATE TABLE statements from database.sql
```

**3. Configure Database Connection**

Edit `config/database.php`:
```php
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';
```

**4. Set Upload Directory Permissions**
```bash
mkdir -p uploads/MindLink/uploads
chmod 755 uploads/MindLink/uploads
```

**5. Access the Application**

- Navigate to `http://yourdomain.com/pages/index.php`
- Or use the welcome page at the root

---

## User Roles & Access

### Regular Student
- Create anonymous posts
- Like and comment on posts
- View resources
- Update profile

### Peer Counselor (Verified)
- All student features
- Post wellness resources
- Special verification badge

### Counselor Admin
- All counselor features
- Manage peer counselor IDs
- View platform statistics
- Access admin dashboard

---

## Default Credentials

**Counselor Admin Account:**
- Email: `counselor@mindlink.com`
- Password: `counselor123`

**Counselor Access Code:**
- Code: `COUNSELOR2025`

**Sample Peer Counselor IDs:**
- PC001 - John Doe
- PC002 - Jane Smith
- PC003 - Emily Johnson

---

##  Project Structure
```
MindLink/
├── config/
│   └── database.php          # Database configuration
├── css/
│   ├── welcome.css           # Landing page styles
│   ├── auth.css              # Authentication pages
│   ├── feed.css              # Main feed styles
│   ├── resources.css         # Resources page
│   ├── profile.css           # Profile management
│   ├── counselor_admin.css   # Admin panel
│   ├── post_detail.css       # Post detail view
│   └── help.css              # Help page
├── js/
│   └── feed.js               # Feed interactions
├── pages/
│   ├── index.php             # Welcome page
│   ├── signup.php            # User registration
│   ├── counselor_signup.php  # Counselor registration
│   ├── login.php             # Authentication
│   ├── feed.php              # Main feed
│   ├── resources.php         # Wellness resources
│   ├── profile.php           # User profile
│   ├── counselor_admin.php   # Admin dashboard
│   ├── post_detail.php       # Post comments view
│   ├── help.php              # FAQ page
│   ├── toggle_like.php       # AJAX like handler
│   └── logout.php            # Session termination
├── images/
│   ├── MindLink.png          # Platform logo
│   └── bg-3.jpg              # Background image
├── fonts/
│   └── BubbleboddyNeue-Light Trial.ttf
└── uploads/                  # User profile pictures
```

---

## Design Features

**Color Scheme:**
- Primary: `#72c8bd` (Teal)
- Secondary: `#017163` (Dark Teal)
- Accent: `#b9e366` (Light Green)

**Features:**
- Custom Typography: Bubblebody Neue font for headings
- Responsive Design: Mobile-friendly layouts
- Smooth Animations: CSS transitions and transforms

---

##  Security Features

- Password hashing using PHP's `password_hash()`
- Session-based authentication
- SQL injection prevention via prepared statements
- File upload validation
- Role-based access control

---

##  Pages Overview

| Page | Description | Access Level |
|------|-------------|--------------|
| Welcome | Landing page with features | Public |
| Signup | Student registration | Public |
| Counselor Signup | Counselor registration | Public |
| Login | User authentication | Public |
| Feed | Main content stream | Authenticated |
| Resources | Mental wellness tips | Authenticated |
| Post Detail | Comments and discussion | Authenticated |
| Profile | User settings | Authenticated |
| Counselor Admin | Management dashboard | Admin only |
| Help | FAQ and support | Authenticated |

---

##  How to Use

### For Students

1. **Sign Up** - Create an account with your name and email
2. **Explore Feed** - View posts from the community
3. **Share Your Thoughts** - Create posts (anonymously or with your name)
4. **Engage** - Like and comment on posts
5. **Access Resources** - Read mental wellness tips from verified counselors

### For Peer Counselors

1. **Get Verified** - Go to Profile settings and enter your Counselor ID
2. **Share Resources** - Post mental wellness content on the Resources page
3. **Support Community** - Engage with student posts

### For Admins

1. **Login** - Use counselor admin credentials
2. **Manage IDs** - Add new peer counselor verification IDs
3. **Monitor Platform** - View statistics and user activity

---

## Contributing

To contribute to MindLink:

1. Test new features thoroughly
2. Maintain code consistency
3. Document changes clearly
4. Respect user privacy and anonymity

---

##  Support

For questions or support:
- Email: deubaybe.dounia@ashesi.edu.gh
- Check the Help page within the platform

---

##  License

This project is created for educational purposes as part of a web development course.

---

##  Future Enhancements

- Real-time chat with counselors
- Mobile app development
- Email notifications
- Advanced content moderation
- Analytics dashboard
- Multi-language support
- Push notifications
- Content filtering and reporting
- Integration with mental health resources
- Posts & Comment reports system

---

## Acknowledgments

Built with love for student mental wellness

*Remember: Your voice matters, and your feelings are valid.*

---

### Welcome Page
The landing page showcases MindLink's core features in an inviting, calming design with a plus (+) arrangement of feature cards.

### Feed Page
Users can create posts, view community content, like posts, and engage through comments. Posts can be shared anonymously for privacy.

### Resources Page
Verified peer counselors share mental wellness tips and guidance. Resources are displayed with special badges to indicate credible content.

### Profile Page
Users can update their information, upload profile pictures, and verify themselves as peer counselors using their credentials.

### Admin Dashboard
Counselor admins can manage peer counselor IDs, view platform statistics, and maintain the verification system.

---
