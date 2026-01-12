# Simple rule-based recommender stub.
# Replace with agronomic logic or a data-driven model later.
def suggest(disease_label: str) -> str:
    d = disease_label.lower()
    if d == "rust":
        return ("Priority: fungicide & sanitation. Nutrients: ensure K and Ca are adequate; "
                "avoid excess N. Consider balanced N-P-K and micronutrients (Zn, B).")
    if d in ("cercospora","phoma"):
        return ("Improve leaf nutrition & stress management. Ensure sufficient K, Mg; "
                "avoid shade/moisture stress. Balanced NPK, add micronutrients if soil tests indicate.")
    if d == "miner":
        return ("Monitor infestation; maintain plant vigor. Balanced NPK. Organic matter helps resilience.")
    return ("Healthy leaf. Follow maintenance fertilization per soil test; balanced N-P-K and organic matter.")
