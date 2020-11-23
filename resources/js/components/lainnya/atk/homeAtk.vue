<template>
  <div class="container">
    <div class="menu-atk">
      <select
        style="border: solid 1px #ccc; padding-left: 1%; color: #666"
        class="kategoriform"
      >
        <option value="">Semua ATK</option>
        <option value="">Kertas</option>
        <option value="">Pulpen</option>
        <option value="">Penghapus</option>
      </select>
    </div>
    <div class="konten">
      <div class="listitem">
        <img src="" alt="" />
        <b>Kertas A4</b>
        <div class="row">
          <button
            style="margin-top: 10px; margin-left: 22%; float: left"
            class="btn-sm btn-success"
          >
            Lihat detail
          </button>
        </div>
      </div>
      <div class="listitem">
        <img src="" alt="" />
        <b>Pulpen Standard Hitam</b>
        <div class="row">
          <button
            style="margin-top: 10px; margin-left: 22%; float: left"
            class="btn-sm btn-success"
          >
            Lihat detail
          </button>
        </div>
      </div>
      <div class="listitem">
        <img src="" alt="" />
        <b>Penghapus Karet</b>
        <div class="row">
          <button
            style="margin-top: 10px; margin-left: 22%; float: left"
            class="btn-sm btn-success"
          >
            Lihat detail
          </button>
        </div>
      </div>
      <div class="listitem">
        <img src="" alt="" />
        <b>Penggaris Besi</b>
        <div class="row">
          <button
            style="margin-top: 10px; margin-left: 22%; float: left"
            class="btn-sm btn-success"
          >
            Lihat detail
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style>
.menu-atk {
  width: 100%;
  border: solid 1px rgb(228, 227, 227);
  padding: 1%;
}

.konten {
  width: 100%;
  height: 440px;
  margin-top: 15px;
  border: solid 1px #e5e6e7;
  overflow-y: scroll;
  background-color: #eff2f5;
  padding: 2%;
}

.kategoriform {
  width: 20%;
  height: 35px;
}

.listitem {
  border: solid 1px rgb(228, 227, 227);
  width: 15%;
  height: 260px;
  margin-right: 1.66%;
  float: left;
  margin-bottom: 15px;
  background-color: #fff;
  border-radius: 3px;
  padding: 1%;
}

.listitem img {
  width: 100%;
  height: 150px;
  border: solid 1px rgb(228, 227, 227);
  float: left;
}

.listitem b {
  float: left;
  margin-top: 10px;
  height: 35px;
  font-size: 0.9em;
  width: 100%;
  text-align: center;
}
</style>
