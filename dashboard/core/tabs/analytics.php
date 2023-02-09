<div class="flex-gallery">
  <aside>
    <div class="offwhite-box">
      <h2>Clients</h2>
      <p>
        La courbe de progression du nombre de vos clients par rapport à l'année actuelle
      </p>

      <menu>
        <canvas class="ak-chart" style="margin-top: 1rem"></canvas>
        <script type="text/javascript">
          (function(){
            const chart = document.currentScript.parentNode.querySelector("canvas.ak-chart");
            new Chart(chart.getContext('2d'), {
              type: "line",
              data: {
                labels: Date.months,
                datasets: [
                  {
                    label: "Inscriptions",
                    data: [0, 10, 5, 25, 32, 31, 10, 40, 42, 60, 65, 68],
                    borderColor: "#2196f3",
                    backgroundColor: "#2196f3"
                  }
                ]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: {display: false},
                  title: {display: false}
                },
                radius: 6
              }
            })
          }());
        </script>
      </menu>
    </div>
  </aside>

  <aside>
    <div class="offwhite-box">
      <h2>Commandes</h2>
      <p>
        La courbe de progression du nombre de commandes par rapport à l'année actuelle
      </p>

      <menu>
        <canvas class="ak-chart" style="margin-top: 1rem"></canvas>
        <script type="text/javascript">
          (function(){
            const chart = document.currentScript.parentNode.querySelector("canvas.ak-chart");
            new Chart(chart.getContext('2d'), {
              type: "bar",
              data: {
                labels: Date.months,
                datasets: [
                  {
                    label: "Total",
                    data: [0, 10, 5, 25, 32, 31, 10, 40, 42, 60, 65, 68],
                    borderColor: "#ff3c7f",
                    backgroundColor: "#ff3c7f"
                  },
                  {
                    label: "Annulés",
                    data: [0, 10, 5, 25, 32, 31, 10, 40, 42, 60, 65, 68],
                    borderColor: "red",
                    backgroundColor: "red"
                  },
                  {
                    label: "Validés",
                    data: [0, 10, 5, 25, 32, 31, 10, 40, 42, 60, 65, 68],
                    borderColor: "green",
                    backgroundColor: "green"
                  }
                ]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: {display: false},
                  title: {display: false}
                }
              }
            })
          }());
        </script>
      </menu>
    </div>
  </aside>

  <aside>
    <div class="offwhite-box">
      <h2>Contacts & Messagerie</h2>
      <p>
        La courbe de progression du nombre de demandes de contacts reçus par rapport à l'année actuelle
      </p>

      <menu>
        <canvas class="ak-chart" style="margin-top: 1rem"></canvas>
        <script type="text/javascript">
          (function(){
            const chart = document.currentScript.parentNode.querySelector("canvas.ak-chart");
            new Chart(chart.getContext('2d'), {
              type: "radar",
              data: {
                labels: Date.months,
                datasets: [
                  {
                    label: "Contacts",
                    data: [0, 10, 5, 25, 32, 31, 10, 40, 42, 60, 65, 68],
                    backgroundColor: "#ff3c7f9e"
                  },
                  {
                    label: "Demande spéciales",
                    data: [32, 10, 42, 0, 31, 65, 10, 40, 5, 68, 25, 60],
                    backgroundColor: "#2196f3ab"
                  }
                ]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: {display: true, position: "right"},
                  title: {display: false}
                },
                radius: 6
              }
            })
          }());
        </script>
      </menu>
    </div>
  </aside>
</div>
