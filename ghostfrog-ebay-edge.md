This is the final, comprehensive **GhostFrog SaaS Factory Concrete Execution Plan**. It consolidates every decision we’ve made—from the technical stack and user journeys to the zero-liability product strategy and credit-based pricing—into one master document for your future reference or to feed directly into a coding assistant.

### **The Master Plan: GhostFrog SaaS Factory**

#### **1. Core Mission**

To build a high-speed "SaaS Factory" that separates the "Body" (Business/UI) from the "Brain" (Agentic AI). This architecture allows for the rapid launch of multiple B2B tools by swapping out Python-based AI agents while reusing a standardized Laravel-based business chassis.

#### **2. The Tech Stack (The "Engine")**

* **The Chassis (Laravel 11 + Livewire 4):**
* **Starter Kit:** Jetstream with **Teams** enabled (essential for B2B/Agency scaling).
* **Frontend:** Livewire + Alpine.js for a fast, "single-page" reactive feel without the complexity of React.
* **Billing:** Stripe Cashier for hybrid subscription and credit management.


* **The Brain (Python 3.11+ + FastAPI):**
* **Orchestration:** LangChain and **LangGraph** for stateful, multi-step reasoning.
* **API Layer:** FastAPI to expose AI endpoints to the Laravel chassis.
* **Persistence:** Postgres checkpoints to allow agents to "pause" and "resume" long-running tasks.
* **Observability:** **Heartbeet** for real-time health monitoring of the AI workers.



#### **3. Product #1: The eBay Niche Gap-Finder**

* **Strategic Pivot:** Moved from high-liability compliance (GPSR/Tax) to **Market Intelligence** to ensure zero legal liability.
* **The Value Prop:** Identifies "The Missing 3"—specific data points or attributes that top-ranking competitors are failing to include in their listings.
* **Technical Edge:** Uses the **eBay Inventory Mapping API (GraphQL)** to find schema gaps and compares them against "Voice of the Customer" review data.

#### **4. The Business Model (Pricing & Credits)**

* **Hybrid Structure:**
* **Pro Subscription (£49/mo):** Includes a baseline of 50 scans/month and team access.
* **Credit Top-ups:** For users who need more power (e.g., **30 scans for £20**).


* **The Credit Loop:** 1 Successful Scan = 1 Credit. Credits are reserved on initiation and only finalized upon a successful webhook return from the Brain.

#### **5. Detailed User Journeys**

* **Regular User (The Seller):**
1. **Register/Join Team:** Standard Jetstream flow.
2. **Fueling:** Purchase a "Scan Pack" via Stripe.
3. **The Loop:** Enter Niche → Click "Run" → Credit Reserved → Agent works in background.
4. **The Result:** Notification received → View "Gap Report" → Download PDF for listing optimization.


* **Admin User (The Owner):**
1. **Revenue Health:** View MRR and credit sales.
2. **System Health:** Monitor the "Heartbeet" of the Python Brain.
3. **Support:** Manually adjust team credits or investigate failed scans.



#### **6. Site Map (The Blueprint)**

* **Landing Page:** Marketing the "Gap Intelligence" edge.
* **Dashboard:** Team activity feed and current credit balance.
* **New Scan:** The primary input interface for keywords/categories.
* **Report View:** Interactive cards showing the "Missing 3" gaps, competitor weaknesses, and actionable listing advice.
* **Scan History:** Searchable archive of all team reports.
* **Billing/Credits:** Stripe Customer Portal for managing subs and buying top-ups.
* **Admin Panel:** System-wide metrics and worker health status.

#### **7. Development Roadmap**

* **Phase 1: The Chassis.** Laravel Jetstream + Stripe Cashier + Credit DB schema.
* **Phase 2: The Bridge.** FastAPI skeleton with a "Hello World" endpoint for Laravel to call.
* **Phase 3: The Brain.** Build the LangGraph node for eBay scraping and LLM gap analysis.
* **Phase 4: The UI.** Finalize the Livewire "Report View" and real-time status updates.
* **Phase 5: Scaling.** Clone the "Factory" for the next product (e.g., Companies House or Customs) once Product #1 is profitable.


