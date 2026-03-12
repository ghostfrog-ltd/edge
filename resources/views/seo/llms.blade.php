# Ghostfrog Ebay Edge

Ghostfrog Ebay Edge is a SaaS product for eBay sellers. It scans a keyword or niche, compares competitor listings, and returns a report showing missing attributes, weak spots, and practical actions.

## Public pages

- Home: {{ route('home') }}
- How It Works: {{ route('how-it-works') }}
- Contact: {{ route('contact.show') }}
- Terms: {{ route('terms.show') }}
- Privacy: {{ route('policy.show') }}

## Product summary

- Input: a keyword or niche to scan
- Unit of value: 1 credit equals 1 successful scan
- Output: a structured report with missing fields, competitor patterns, and recommended listing actions
- Application shape: Laravel handles auth, teams, credits, billing, and reports; Python handles evidence gathering, comparison logic, and LLM-assisted prioritization

## Audience

Ghostfrog Ebay Edge is built for eBay sellers who want clearer listing intelligence before rewriting titles, item specifics, and offer positioning.

## Contact

Support requests and product questions can be sent through the contact page: {{ route('contact.show') }}
