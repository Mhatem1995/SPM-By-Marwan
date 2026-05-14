=== SPM by Marwan ===
Contributors: marwanhatem
Donate link: https://ko-fi.com/marwanhatem31477
Tags: performance, monitoring, security, diagnostics, profiler
Requires at least: 5.6
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Tracks load time and PHP errors for each active extension, storing logs in a custom database table.

== Description ==

SPM by Marwan is a professional-grade diagnostics and intelligence platform designed for WordPress administrators and developers. It provides deep visibility into your WordPress ecosystem, offering real-time monitoring, security auditing, and performance profiling to ensure your site remains fast, secure, and stable.

### The Problem

Managing a modern WordPress ecosystem is inherently complex. Site owners and developers frequently struggle with:

*   **Slow Extensions:** Identifying which specific extension is degrading page load times or consuming excessive server resources.
*   **Hidden PHP Errors:** Unseen warnings and fatal errors that silently break functionality or create vulnerabilities.
*   **Security Risks:** Unmaintained, abandoned, or vulnerable code exposing the site to potential exploits.
*   **Trust Issues:** Uncertainty regarding an extension's origin, development standards, and code quality.
*   **Update Degradation:** Performance regressions and unexpected conflicts introduced immediately after updates.

### Core Features

*   **Real-Time Monitoring Engine:** Actively tracks resource consumption, database queries, and execution times for every active extension. Identifies performance bottlenecks as they happen, preventing systemic slowdowns.
*   **Performance Diagnostics:** Deep-dive profiling for individual extensions. Analyzes memory footprint and script execution to provide actionable intelligence on resource utilization.
*   **Security Scanner:** Proactively audits installed extensions against known vulnerability databases and flags high-risk code patterns. Ensures your stack adheres to strict security standards.
*   **Trust Verification:** Evaluates code based on developer reputation, update frequency, repository standing, and code integrity. Establishes a trust score to help you make informed decisions about your technology stack.
*   **Historical Analytics:** Maintains a robust ledger of performance and security metrics over time. Visualizes trends to help you understand the long-term impact of specific extensions and updates.
*   **Action Layer (Scan / Safe Mode / Reporting):** Provides immediate remediation tools. Execute targeted scans, isolate problematic extensions using Safe Mode, and generate comprehensive diagnostics reports.

== Installation ==

1. Upload the `spm-by-marwan` folder to your `/wp-content/plugins/` directory.
2. Navigate to the **Plugins** menu in WordPress and activate "SPM by Marwan".
3. Locate the new **Smart Monitor** dashboard in your WordPress admin menu to begin profiling your site.

== External Services ==

This plugin utilizes the WordPress.org Plugins API (https://api.wordpress.org) to verify plugin licenses and retrieve information about installed plugins.
* **What data is sent:** The names and slugs of your active plugins are sent in the API request.
* **When it is sent:** Data is sent when the plugin performs a scheduled background scan or when you manually click "Deep Scan" or "Refresh Now" on the dashboard.
* **Why it is used:** To verify that the plugins running on your site are authentic, properly licensed, and available on the official WordPress.org directory, which enhances your site's security and trust metrics.

For more information, please refer to the [WordPress.org Privacy Policy](https://wordpress.org/about/privacy/).

== Frequently Asked Questions ==

= Does the monitor degrade my site's performance? =

No. SPM by Marwan is built on a robust, scalable foundation designed for minimal overhead. It uses lightweight hooks into the WordPress core to gather metrics without degrading performance, and asynchronous analysis modules that run non-blocking audits.

== Screenshots ==

1. Dashboard Overview displaying system health and critical alerts.
2. Granular performance breakdown of individual extensions.
3. Detailed vulnerability reporting and security recommendations.
4. Long-term performance trends and update impact visualization.

== Changelog ==

= 1.2.0 =
* Major structural refactoring for WordPress.org compliance.
* Extracted inline CSS into enqueued files.
* Removed development error_reporting overrides.
* Synchronized global text-domain to spm-by-marwan.
* Strengthened security and escaping across all template files.

= 1.0.0 =
* Initial release.
