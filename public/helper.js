document.getElementById('input4').addEventListener('change', updateUI);

function updateUI() {
    var selectedValue = this.value;

    // Reset UI
    document.getElementById('a_group').style.display = 'none';
    document.getElementById('b_group').style.display = 'none';
    document.getElementById('c_group').style.display = 'none';
    document.getElementById('d_group').style.display = 'none';
    document.getElementById('over_title').style.display = 'block';
    document.getElementById('under_title').style.display = 'block';
    document.getElementById('a_label').innerHTML = 'Option A';
    document.getElementById('b_label').innerHTML = 'Option B';
    document.getElementById('a_label').style.display = 'block';
    if (document.getElementById('a_option') != null) {
        document.getElementById('a_option').style.display = 'none';
        document.getElementById('b_option').style.display = 'none';
        document.getElementById('c_option').style.display = 'none';
        document.getElementById('d_option').style.display = 'none';
        document.getElementById('a_option').disabled = true;
        document.getElementById('b_option').disabled = true;
        document.getElementById('c_option').disabled = true;
        document.getElementById('d_option').disabled = true;
    }

    if (selectedValue === 'Quick Pick') {
        document.getElementById('a_group').style.display = 'block';
        document.getElementById('b_group').style.display = 'block';
        document.getElementById('c_group').style.display = 'block';
        document.getElementById('d_group').style.display = 'block';

        if (document.getElementById('a_option') != null) {
            document.getElementById('a_option').style.display = 'block';
            document.getElementById('b_option').style.display = 'block';
            document.getElementById('c_option').style.display = 'block';
            document.getElementById('d_option').style.display = 'block';
            document.getElementById('a_option').disabled = false;
            document.getElementById('b_option').disabled = false;
            document.getElementById('c_option').disabled = false;
            document.getElementById('d_option').disabled = false;
        }
    } else if (selectedValue === 'Over/Under') {
        document.getElementById('over_title').style.display = 'none';
        document.getElementById('under_title').style.display = 'none';
        document.getElementById('a_group').style.display = 'block';
        document.getElementById('b_group').style.display = 'block';
        document.getElementById('a_label').innerHTML = 'Over Odds';
        document.getElementById('b_label').innerHTML = 'Under Odds';

        if (document.getElementById('a_option') != null) {
            document.getElementById('a_option').style.display = 'block';
            document.getElementById('b_option').style.display = 'block';
            document.getElementById('a_option').disabled = false;
            document.getElementById('b_option').disabled = false;
        }
    } else  {
        document.getElementById('a_group').style.display = 'block';
        document.getElementById('over_title').style.display = 'none';
        document.getElementById('a_label').innerHTML = 'Odds';
        
        if (document.getElementById('a_option') != null) {
            document.getElementById('a_option').style.display = 'block';
            document.getElementById('b_option').style.display = 'block';
            document.getElementById('a_option').disabled = false;
            document.getElementById('b_option').disabled = false;
        }
    }
}