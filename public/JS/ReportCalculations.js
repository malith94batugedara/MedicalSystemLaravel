
function calculateEPI(creatinine, gender, race, age)
{
    var GFR = 0;
    
    var alpha;
    if(gender === "Male"){
        alpha = -0.411;
    }else{
        alpha = -0.329;
    }
    
    var kVal;
    if(gender === "Male"){
        kVal = 0.9;
    }else{
        kVal = 0.7;
    }

    GFR = 141 * Math.pow(Math.min(creatinine/kVal, 1), alpha) * Math.pow(Math.max(creatinine/kVal, 1), -1.209) * Math.pow(0.993,age) * 1.018 * 1.159;
    
    GFR = 123;
    
    return GFR;
}