
class BubbleChart
  constructor: (data) ->
    @data = data
    @width = 940
    @height = 700

    @tooltip = CustomTooltip("gates_tooltip", 240)

    # locations the nodes will move towards
    # depending on which view is currently being
    # used
    @center = {
      "cancer": {x: @width / 0.8, y: @height / 0.8},
      "cholera": {x: @width / 0.8, y: @height / 0.8},
      "death": {x: @width / 0.8, y: @height / 0.8},
      "diabetes": {x: @width / 0.8, y: @height / 0.8},
      "disease": {x: @width / 0.8, y: @height / 0.8},
      "finance": {x: @width / 0.8, y: @height / 0.8},
      "healthcare": {x: @width / 0.8, y: @height / 0.8},
      "heart": {x: @width / 0.8, y: @height / 0.8},
      "hygiene": {x: @width / 0.8, y: @height / 0.8},
      "malnutrition": {x: @width / 0.8, y: @height / 0.8},
      "mortality": {x: @width / 0.8, y: @height / 0.8},
      "obesity": {x: @width / 0.8, y: @height / 0.8},
      "population": {x: @width / 0.8, y: @height / 0.8},
      "sanitation": {x: @width / 0.8, y: @height / 0.8},
      "workforce": {x: @width / 0.8, y: @height / 0.8},
      "overall": {x: @width / 2, y: @height / 2}}
    @year_centers = {
      "cancer": {x: @width / 4.5, y: @height / 3.2},
      "cholera": {x: @width / 2.8, y: @height / 3.2},
      "death": {x: @width / 2, y: @height / 3.2},
      "diabetes": {x: @width / 1.7, y: @height / 3.2},
      "disease": {x: @width / 1.28, y: @height / 3.2},
      "finance": {x: @width / 4.5, y: @height / 2.2},
      "healthcare": {x: @width / 2.8, y: @height / 2.2},
      "heart": {x: @width / 2, y: @height / 2.2},
      "hygiene": {x: @width / 1.7, y: @height / 2.2},
      "malnutrition": {x: @width / 1.28, y: @height / 2.2},
      "mortality": {x: @width / 4.5, y: @height / 1.5},
      "obesity": {x: @width / 2.8, y: @height / 1.5},
      "population": {x: @width / 2, y: @height / 1.5},
      "sanitation": {x: @width / 1.7, y: @height / 1.5},
      "workforce": {x: @width / 1.28, y: @height / 1.5},
      "overall": {x: @width / 0.6, y:@height / 0.6}
    }

    # used when setting up force and
    # moving around nodes
    @layout_gravity = -0.01
    @damper = 0.1

    # these will be set in create_nodes and create_vis
    @vis = null
    @nodes = []
    @force = null
    @circles = null

    # nice looking colors - no reason to buck the trend
    @fill_color = d3.scale.ordinal()
      .domain(["low", "medium", "high"])
      .range(["#beccae", "white", "skyblue"])

    # use the max total_amount in the data as the max in the scale's domain
    max_amount = d3.max(@data, (d) -> parseInt(d.total_amount))
    @radius_scale = d3.scale.pow().exponent(0.5).domain([0, max_amount]).range([2, 60])
    
    this.create_nodes()
    this.create_vis()

  # create node objects from original data
  # that will serve as the data behind each
  # bubble in the vis, then add each node
  # to @nodes to be used later
  create_nodes: () =>
    @data.forEach (d) =>
      node = {
        id: d.id
        radius: @radius_scale(parseInt(d.total_amount))
        value: d.total_amount
        name: d.keyword
        group: d.group
        category: d.category
        x: Math.random() * 900
        y: Math.random() * 800
      }
      @nodes.push node

    @nodes.sort (a,b) -> b.value - a.value


  # create svg at #vis and then 
  # create circle representation for each node
  create_vis: () =>
    @vis = d3.select("#vis").append("svg")
      .attr("width", @width)
      .attr("height", @height)
      .attr("id", "svg_vis")

    @circles = @vis.selectAll("circle")
      .data(@nodes, (d) -> d.id)

    # used because we need 'this' in the 
    # mouse callbacks
    that = this

    # radius will be set to 0 initially.
    # see transition below
    @circles.enter().append("circle")
      .attr("r", 0)
      .attr("fill", (d) => @fill_color(d.group))
      .attr("stroke-width", 2)
      .attr("stroke", (d) => d3.rgb(@fill_color(d.group)).darker())
      .attr("id", (d) -> "bubble_#{d.id}")
      .on("mouseover", (d,i) -> that.show_details(d,i,this))
      .on("mouseout", (d,i) -> that.hide_details(d,i,this))

    # Fancy transition to make bubbles appear, ending with the
    # correct radius
    @circles.transition().duration(2000).attr("r", (d) -> d.radius)


  # Charge function that is called for each node.
  # Charge is proportional to the diameter of the
  # circle (which is stored in the radius attribute
  # of the circle's associated data.
  # This is done to allow for accurate collision 
  # detection with nodes of different sizes.
  # Charge is negative because we want nodes to 
  # repel.
  # Dividing by 8 scales down the charge to be
  # appropriate for the visualization dimensions.
  charge: (d) ->
    -Math.pow(d.radius, 2.0) / 8

  # Starts up the force layout with
  # the default values
  start: () =>
    @force = d3.layout.force()
      .nodes(@nodes)
      .size([@width, @height])

  # Sets up force layout to display
  # all nodes in one circle.
  display_group_all: () =>
    @force.gravity(@layout_gravity)
      .charge(this.charge)
      .friction(0.9)
      .on "tick", (e) =>
        @circles.each(this.move_towards_center(e.alpha))
          .attr("cx", (d) -> d.x)
          .attr("cy", (d) -> d.y)
    @force.start()

    this.hide_years()

  # Moves all circles towards the @center
  # of the visualization
  move_towards_center: (alpha) =>
    (d) => 
      target = @center[d.category]
      d.x = d.x + (target.x - d.x) * (@damper + 0.02) * alpha
      d.y = d.y + (target.y - d.y) * (@damper + 0.02) * alpha

  # sets the display of bubbles to be separated
  # into each year. Does this by calling move_towards_year
  display_by_year: () =>
    @force.gravity(@layout_gravity)
      .charge(this.charge)
      .friction(0.9)
      .on "tick", (e) =>
        @circles.each(this.move_towards_year(e.alpha))
          .attr("cx", (d) -> d.x)
          .attr("cy", (d) -> d.y)
    @force.start()

    this.display_years()

  # move all circles to their associated @year_centers 
  move_towards_year: (alpha) =>
    (d) =>
      target = @year_centers[d.category]
      d.x = d.x + (target.x - d.x) * (@damper + 0.02) * alpha * 1.1
      d.y = d.y + (target.y - d.y) * (@damper + 0.02) * alpha * 1.1






  # Method to display year titles
  display_years: () =>
    years_x = {"Cancer": 100, "Cholera": 300 ,"Death": @width / 2, "Diabetes": 2 * @width / 3 ,"Disease": @width - 100,"Finance": 100, "Health Care": 300 ,"Heart": @width / 2, "Hygiene": 2 * @width / 3 ,"Malntition": @width - 100,"Mortality": 100, "Obesity": 300 ,"Population": @width / 2, "Sanitation": 2 * @width / 3 ,"Workforce": @width - 100}
    years_y = {"Cancer": 40, "Cholera": 40, "Death": 40, "Diabetes": 40, "Disease": 40,"Finance": 260, "Health Care": 260, "Heart": 260, "Hygiene": 260, "Malntition": 260, "Mortality": 440, "Obesity": 440, "Population": 440, "Sanitation": 440, "Workforce": 440}
    years_data = d3.keys(years_x)
    years = @vis.selectAll(".years")
      .data(years_data)

    years.enter().append("text")
      .attr("class", "years")
      .attr("x", (d) => years_x[d] )
      .attr("y", (d) => years_y[d])
      .attr("text-anchor", "middle")
      .text((d) -> d)

  # Method to hide year titiles
  hide_years: () =>
    years = @vis.selectAll(".years").remove()

  show_details: (data, i, element) =>
    d3.select(element).attr("stroke", "black")
    content = "<span class=\"name\">Word:</span><span class=\"value\"> #{data.name}</span><br/>"
    content +="<span class=\"name\">Amount:</span><span class=\"value\"> #{addCommas(data.value)}</span><br/>"
    content +="<span class=\"name\">Category:</span><span class=\"value\"> #{data.category}</span>"
    @tooltip.showTooltip(content,d3.event)


  hide_details: (data, i, element) =>
    d3.select(element).attr("stroke", (d) => d3.rgb(@fill_color(d.group)).darker())
    @tooltip.hideTooltip()


root = exports ? this

$ ->
  chart = null

  render_vis = (csv) ->
    chart = new BubbleChart csv
    chart.start()
    root.display_all()
  root.display_all = () =>
    chart.display_group_all()
  root.display_year = () =>
    chart.display_by_year()
  root.display_network = () =>
    chart.display_network()
  root.toggle_view = (view_type) =>

    if view_type == 'year'
      root.display_year()
    else 
      root.display_all()

  d3.csv "data/Book.csv", render_vis
